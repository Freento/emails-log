<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Plugin\Model;

use Exception;
use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Helper\Config;
use Freento\EmailsLog\Model\AdditionalInfoStorage;
use Freento\EmailsLog\Model\LogFactory;
use Freento\EmailsLog\Model\LogRepository;
use Freento\EmailsLog\Model\Source\Status;
use Magento\Email\Model\Transport;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\EmailMessageInterface;

class TransportPlugin
{
    /**
     * @param Config $helperConfig
     * @param LogFactory $logFactory
     * @param LogRepository $logRepository
     * @param AdditionalInfoStorage $additionalInfoStorage
     */
    public function __construct(
        private readonly Config $helperConfig,
        private readonly LogFactory $logFactory,
        private readonly LogRepository $logRepository,
        private readonly AdditionalInfoStorage $additionalInfoStorage
    ) {
    }

    /**
     * If a module is active while sending a message, we logging it
     *
     * @param Transport $transport
     * @param callable $proceed
     * @return void
     * @throws AlreadyExistsException
     * @throws MailException
     */
    public function aroundSendMessage(Transport $transport, callable $proceed): void
    {
        if (!$this->helperConfig->isModuleEnabled()) {
            $proceed();
        } else {
            $this->logMessage($transport, $proceed);
        }
    }

    /**
     * Log email
     *
     * @param Transport $transport
     * @param callable $proceed
     * @return void
     * @throws AlreadyExistsException
     * @throws MailException
     */
    private function logMessage(Transport $transport, callable $proceed): void
    {
        $status = Status::STATUS_SUCCESS;
        $message = $transport->getMessage();

        try {
            $proceed();
        } catch (Exception $e) {
            $status = Status::STATUS_FAILED;
            throw new MailException(__($e->getMessage()), $e);
        } finally {
            $recipient = current($message->getTo())->getEmail();
            $subject = $message->getSubject();
            $content = $this->getContent($message);
            $bcc = $this->getBcc($message);

            $log = $this->logFactory->create();
            $log->setRecipient($recipient);
            $log->setBcc($bcc);
            $log->setSubject($subject);
            $log->setStatus($status);

            $transportId = spl_object_hash($transport);
            $additionalInfo = $this->additionalInfoStorage->getData($transportId);

            if ($additionalInfo->getData(LogInterface::TEMPLATE)) {
                $log->setTemplate($additionalInfo->getData(LogInterface::TEMPLATE));
            }

            if ($additionalInfo->getData(LogInterface::STORE_ID)) {
                $log->setStore($additionalInfo->getData(LogInterface::STORE_ID));
            }

            if ($this->helperConfig->isContentSaveNeeded()) {
                $log->setContent($content);
            }

            $this->logRepository->save($log);
        }
    }

    /**
     * Get BCC as string
     *
     * @param EmailMessageInterface $message
     * @return string
     */
    private function getBcc(EmailMessageInterface $message): string
    {
        $bcc = $message->getBcc();

        return $bcc ? implode(', ', array_map(fn($address) => $address->getEmail(), $bcc)) : '';
    }

    /**
     * Get message body as string
     *
     * Magento 2.4.8 deprecated the use of the laminas/mail module in favor of the symfony/component module.
     * Although we could use the $message->getBodyText() method, which is intended to be compatible with both
     * implementations, it fails in Magento 2.4.8-p1 due to an incorrect class being used. $message->getBodyText() calls
     * $this->symfonyMessage->getTextBody() internally, however, $this->symfonyMessage is an instance of
     * Symfony\Component\Mime\Message, which does not have a getTextBody() method. The getTextBody() method exists in
     * the Symfony\Component\Mime\Email class, which extends Message, but is not used here.
     *
     * @param EmailMessageInterface $message
     * @return string
     */
    private function getContent(EmailMessageInterface $message): string
    {
        // Use getBodyText() only if we don't have symphony message inside $message
        if (!method_exists($message, 'getSymfonyMessage')) {
            return $message->getBodyText();
        }

        // Else work with symphony message directly
        return $message->getSymfonyMessage()?->getBody()?->bodyToString() ?? '';
    }
}
