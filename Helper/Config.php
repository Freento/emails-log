<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    private const XML_PATH_EMAILSLOG_DAYS_BEFORE_CLEAN = 'emailslog/general/days_before_clean';
    private const XML_PATH_EMAILSLOG_SAVE_EMAIL_CONTENT = 'emailslog/general/save_email_content';
    private const XML_PATH_EMAILSLOG_ENABLED = 'emailslog/general/enabled';
    private const XML_PATH_EMAILSLOG_LOG_WHEN_EMAILS_DISABLED = 'emailslog/general/log_when_emails_disabled';
    private const XML_PATH_SYSTEM_SMTP_DISABLE = 'system/smtp/disable';

    /**
     * Return the value of a specific setting depending on the Store ID.
     *
     * @param string $configPath
     * @param int|null $storeId
     * @return string|null
     */
    private function getConfigValue(string $configPath, ?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Returns the "days before clean" value
     *
     * @param int|null $storeId
     * @return int
     */
    public function getDaysBeforeClean(?int $storeId = null): int
    {
        return (int) $this->getConfigValue(self::XML_PATH_EMAILSLOG_DAYS_BEFORE_CLEAN, $storeId);
    }

    /**
     * Checks if email content save necessary
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isContentSaveNeeded(?int $storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_EMAILSLOG_SAVE_EMAIL_CONTENT, $storeId);
    }

    /**
     * Checks if module enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isModuleEnabled(?int $storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_EMAILSLOG_ENABLED, $storeId);
    }

    /**
     * Checks if emails must be logged while the core "Disable Email Communications" setting is on
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isLogWhenEmailsDisabled(?int $storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_EMAILSLOG_LOG_WHEN_EMAILS_DISABLED, $storeId);
    }

    /**
     * Checks the core "Disable Email Communications" setting, the same way the core EmailDisable plugin does
     *
     * @return bool
     */
    public function isEmailCommunicationDisabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SYSTEM_SMTP_DISABLE, ScopeInterface::SCOPE_STORE);
    }
}
