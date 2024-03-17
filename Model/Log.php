<?php

namespace Freento\EmailsLog\Model;

use Freento\EmailsLog\Api\Data\LogInterface;
use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel implements LogInterface
{
    /**
     * @var string
     */
    protected $_cacheTag = 'emailslog_log';

    /**
     * @var string
     */
    protected $_eventPrefix = 'emailslog_log';

    public const DATA_PERSISTOR_KEY = 'email_log';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Log::class);
    }

    /**
     * @inheritDoc
     */
    public function getLogId(): int
    {
        return (int)$this->getData(self::LOG_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLogId(int $logId): void
    {
        $this->setData(self::LOG_ID, $logId);
    }

    /**
     * @inheritDoc
     */
    public function getRecipient(): string
    {
        return $this->getData(self::RECIPIENT);
    }

    /**
     * @inheritDoc
     */
    public function setRecipient(string $recipient): void
    {
        $this->setData(self::RECIPIENT, $recipient);
    }

    /**
     * @inheritDoc
     */
    public function getBcc(): ?string
    {
        return $this->getData(self::BCC);
    }

    /**
     * @inheritDoc
     */
    public function setBcc(string $bcc): void
    {
        $this->setData(self::BCC, $bcc);
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): string
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * @inheritDoc
     */
    public function setSubject(string $subject): void
    {
        $this->setData(self::SUBJECT, $subject);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $content = $this->getData(self::CONTENT);

        return $content !== null ? quoted_printable_decode($content) : '';
    }

    /**
     * @inheritDoc
     */
    public function setContent(string $content): void
    {
        $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus(string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }
}
