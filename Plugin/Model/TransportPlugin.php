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
use Laminas\Mail\Message;
use Magento\Email\Model\Transport;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\MailException;

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
    public function aroundSendMessage(Transport $transport, callable $proceed)
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
        $laminasMessage = Message::fromString($transport->getMessage()->getRawMessage())->setEncoding('utf-8');

        try {
            $proceed();
        } catch (Exception $e) {
            $status = Status::STATUS_FAILED;
            throw new MailException(__($e->getMessage()), $e);
        } finally {
            $recipient = $laminasMessage->getTo()->current()->getEmail();
            $subject = $laminasMessage->getSubject();
            $content = $laminasMessage->getBody();
            $bcc = iterator_to_array($laminasMessage->getBcc());
            $bcc = implode(', ', array_keys($bcc));

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
}
