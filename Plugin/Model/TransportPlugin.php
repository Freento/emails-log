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
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\EmailMessageInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class TransportPlugin
{
    /**
     * @param Config $helperConfig
     * @param LogFactory $logFactory
     * @param LogRepository $logRepository
     * @param AdditionalInfoStorage $additionalInfoStorage
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly Config $helperConfig,
        private readonly LogFactory $logFactory,
        private readonly LogRepository $logRepository,
        private readonly AdditionalInfoStorage $additionalInfoStorage,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * If a module is active while sending a message, we logging it
     *
     * @param Transport $transport
     * @param callable $proceed
     * @return void
     * @throws MailException|Throwable
     */
    public function aroundSendMessage(Transport $transport, callable $proceed): void
    {
        // Status to log the email with; null means the email must not be logged
        $status = null;

        if ($this->helperConfig->isModuleEnabled()) {
            if (!$this->helperConfig->isEmailCommunicationDisabled()) {
                $status = Status::STATUS_SUCCESS;
            } elseif ($this->helperConfig->isLogWhenEmailsDisabled()) {
                // The core EmailDisable plugin swallows the sending inside $proceed()
                $status = Status::STATUS_NOT_SENT;
            }
        }

        if ($status === null) {
            $proceed();
        } else {
            $this->logMessage($transport, $proceed, $status);
        }
    }

    /**
     * Send the email and log it
     *
     * A failure to write the log never affects sending: see saveLog().
     *
     * @param Transport $transport
     * @param callable $proceed
     * @param string $status status to log if sending does not fail
     * @return void
     * @throws MailException|Throwable
     */
    private function logMessage(Transport $transport, callable $proceed, string $status): void
    {
        try {
            $proceed();
        } catch (Throwable $e) {
            $this->saveLog($transport, Status::STATUS_FAILED);
            // MailException accepts only an Exception as the cause, so PHP errors are rethrown as they are
            throw $e instanceof Exception ? new MailException(__($e->getMessage()), $e) : $e;
        }

        $this->saveLog($transport, $status);
    }

    /**
     * Write the email to the log; never throws, errors go to the system log instead
     *
     * @param Transport $transport
     * @param string $status
     * @return void
     */
    private function saveLog(Transport $transport, string $status): void
    {
        try {
            $message = $transport->getMessage();

            $log = $this->logFactory->create();
            $log->setRecipient($this->getRecipients($message));
            $log->setBcc($this->getBcc($message));
            $log->setSubject((string)$message->getSubject());
            $log->setStatus($status);

            $transportId = spl_object_hash($transport);
            $additionalInfo = $this->additionalInfoStorage->getData($transportId);

            if ($additionalInfo->getData(LogInterface::TEMPLATE)) {
                $log->setTemplate($additionalInfo->getData(LogInterface::TEMPLATE));
            }

            // Store id "0" is the admin store and must be logged too, so no plain truthiness check here
            $storeId = $additionalInfo->getData(LogInterface::STORE_ID);
            if ($storeId !== null && $storeId !== '') {
                $log->setStore($storeId);
            }

            if ($this->helperConfig->isContentSaveNeeded()) {
                $log->setContent($this->getContent($message));
            }

            $this->logRepository->save($log);
        } catch (Throwable $e) {
            $this->logger->error(
                'Freento_EmailsLog: failed to save email log: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }
    }

    /**
     * Get all "To" recipients as a comma-separated string
     *
     * @param EmailMessageInterface $message
     * @return string
     */
    private function getRecipients(EmailMessageInterface $message): string
    {
        return implode(', ', array_map(fn($address) => $address->getEmail(), $message->getTo()));
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
