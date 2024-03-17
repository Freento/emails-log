<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Plugin\Model;

use Exception;
use Freento\EmailsLog\Helper\Config;
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
     */
    public function __construct(
        private readonly Config $helperConfig,
        private readonly LogFactory $logFactory,
        private readonly LogRepository $logRepository
    ) {
    }

    /**
     * If a module is active while sending a message, we logging it
     *
     * @param Transport $transport
     * @param callable $proceed
     * @throws MailException
     * @throws AlreadyExistsException
     * @return void
     */
    public function aroundSendMessage(Transport $transport, callable $proceed): void
    {
        if (!$this->helperConfig->isModuleEnabled()) {
            return;
        }

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

            if ($this->helperConfig->isContentSaveNeeded()) {
                $log->setContent($content);
            }

            $this->logRepository->save($log);
        }
    }
}
