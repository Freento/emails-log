<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Api\Data;

interface LogInterface
{
    public const LOG_ID = 'log_id';
    public const RECIPIENT = 'recipient';
    public const BCC = 'bcc';
    public const SUBJECT = 'subject';
    public const CONTENT = 'content';
    public const STATUS = 'status';
    public const TEMPLATE = 'template';
    public const STORE_ID = 'store_id';
    public const CREATED_AT = 'created_at';

    /**
     * Return log id
     *
     * @return int
     */
    public function getLogId(): int;

    /**
     * Set Log ID
     *
     * @param int $logId
     * @return void
     */
    public function setLogId(int $logId): void;

    /**
     * Return recipient
     *
     * @return string
     */
    public function getRecipient(): string;

    /**
     * Set recipient
     *
     * @param string $recipient
     * @return void
     */
    public function setRecipient(string $recipient): void;

    /**
     * Return bcc
     *
     * @return string|null
     */
    public function getBcc(): ?string;

    /**
     * Set bcc
     *
     * @param string $bcc
     * @return void
     */
    public function setBcc(string $bcc): void;

    /**
     * Return subject
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * Set subject
     *
     * @param string $subject
     * @return void
     */
    public function setSubject(string $subject): void;

    /**
     * Return content
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * Set content
     *
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void;

    /**
     * Return status
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Set status
     *
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void;

    /**
     * Return template
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Set template
     *
     * @param string $template
     * @return void
     */
    public function setTemplate(string $template): void;

    /**
     * Return store
     *
     * @return string
     */
    public function getStore(): string;

    /**
     * Set store
     *
     * @param string $store
     * @return void
     */
    public function setStore(string $store): void;

    /**
     * Return created at
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt(string $createdAt): void;
}
