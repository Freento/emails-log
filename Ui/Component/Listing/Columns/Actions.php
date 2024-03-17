<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Ui\Component\Listing\Columns;

use Freento\EmailsLog\Api\Data\LogInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Adds view link
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item[LogInterface::LOG_ID])) {
                    $item[$name]['view'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'emailslog/log/view',
                            [LogInterface::LOG_ID => $item[LogInterface::LOG_ID]]
                        ),
                        'label' => __('View')
                    ];
                }
            }
        }

        return $dataSource;
    }
}
