<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Ui\Component\Listing\Columns;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Psr\Log\LoggerInterface;

class StoreView extends Column
{
    /**
     * Constructor
     *
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param mixed[] $components
     * @param mixed[] $data
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }
        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item): string
    {
        if (!isset($item['store_id']) || ($item['store_id'] !== 0 && !$item['store_id'])) {
            return '-';
        }

        try {
            $storeName = $this->storeManager->getStore($item['store_id'])->getName();
        } catch (NoSuchEntityException $e) {
            $this->logger->debug('Freento_EmailsLog - ' . $e->getMessage(), ['trace' => $e->getTrace()]);
            $storeName = (string)$item['store_id'];
        }

        return $storeName;
    }
}
