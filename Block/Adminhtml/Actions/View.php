<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Block\Adminhtml\Actions;

use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Helper\Config;
use Freento\EmailsLog\Model\Log;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class View extends Container
{
    /**
     * @param Context $context
     * @param Config $helperConfig
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly Config $helperConfig,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Returns a current email log
     *
     * @return LogInterface
     */
    public function getEmailLog(): LogInterface
    {
        return $this->dataPersistor->get(Log::DATA_PERSISTOR_KEY);
    }

    /**
     * Returns current Email Log ID
     *
     * @return int
     */
    public function getCurrentLogId(): int
    {
        $emailLogId = 0;
        $emailLog = $this->dataPersistor->get(Log::DATA_PERSISTOR_KEY);
        if ($emailLog !== null) {
            $emailLogId = $emailLog->getLogId();
        }

        return $emailLogId;
    }

    /**
     * Returns the config url
     *
     * @return string
     */
    public function getConfigUrl(): string
    {
        return $this->getUrl('adminhtml/system_config/edit/section/emailslog');
    }

    /**
     * Return iframe url
     *
     * @return string
     */
    public function getIframeUrl(): string
    {
        return $this->getUrl('emailslog/log/iframe/', [LogInterface::LOG_ID => $this->getCurrentLogId()]);
    }

    /**
     * Checks if iframe is displayable
     *
     * @return bool
     */
    public function isIframeDisplayable(): bool
    {
        return $this->helperConfig->isIframeDisplayable();
    }

    /**
     * Get store label
     *
     * @param LogInterface $log
     * @return string
     */
    public function getStoreLabel(LogInterface $log): string
    {
        if (!$log->getStore()) {
            return '-';
        }

        try {
            $storeName = $this->storeManager->getStore($log->getStore())->getName();
        } catch (NoSuchEntityException $e) {
            $this->logger->debug('Freento_EmailsLog - ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            $storeName = $log->getStore();
        }

        return $storeName;
    }
}
