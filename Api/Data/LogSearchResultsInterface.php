<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

interface LogSearchResultsInterface extends SearchResultInterface
{
    /**
     * Return array of items
     *
     * @return array
     */
    public function getItems();

    /**
     * Sets items by array of items
     *
     * @param array $items
     * @return array
     */
    public function setItems(array $items);
}
