<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Block\Adminhtml\Log;

use Freento\EmailsLog\Helper\Config;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;

class Log extends Container
{
    /**
     * @param Config $helperConfig
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        private readonly Config $helperConfig,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Returns days count before deletion of current log
     *
     * @return int
     */
    public function getDaysBeforeClean(): int
    {
        return $this->helperConfig->getDaysBeforeClean();
    }

    /**
     * Returns the config url
     *
     * @return string
     */
    public function getConfigUrl(): string
    {
        return $this->getUrl('adminhtml/system_config/edit/section/emailslog/');
    }
}
