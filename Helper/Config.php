<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    private const XML_PATH_EMAILSLOG_DAYS_BEFORE_CLEAN = 'emailslog/general/days_before_clean';
    private const XML_PATH_EMAILSLOG_SHOW_IFRAME = 'emailslog/general/show_iframe';
    private const XML_PATH_EMAILSLOG_SAVE_EMAIL_CONTENT = 'emailslog/general/save_email_content';
    private const XML_PATH_EMAILSLOG_ENABLED = 'emailslog/general/enabled';

    /**
     * Return the value of a specific setting depending on the Store ID.
     *
     * @param string $configPath
     * @param int|null $storeId
     * @return string|null
     */
    private function getConfigValue(string $configPath, int $storeId = null): ?string
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
    public function getDaysBeforeClean(int $storeId = null): int
    {
        return (int) $this->getConfigValue(self::XML_PATH_EMAILSLOG_DAYS_BEFORE_CLEAN, $storeId);
    }

    /**
     * Checks if we need to show an iframe
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isIframeDisplayable(int $storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_EMAILSLOG_SHOW_IFRAME, $storeId);
    }

    /**
     * Checks if email content save necessary
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isContentSaveNeeded(int $storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_EMAILSLOG_SAVE_EMAIL_CONTENT, $storeId);
    }

    /**
     * Checks if module enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isModuleEnabled(int $storeId = null): bool
    {
        return (bool)$this->getConfigValue(self::XML_PATH_EMAILSLOG_ENABLED, $storeId);
    }
}
