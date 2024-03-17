<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Model;

use Freento\EmailsLog\Model\ResourceModel\Log\Collection;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\SearchResultInterface;

abstract class AbstractRepository
{
    /**
     * Adds filters to the Log collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResourceModel\Log\Collection $collection
     * @return void
     */
    public function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection): void
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Adds sort order to the Log collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResourceModel\Log\Collection $collection
     * @return void
     */
    public function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection): void
    {
        foreach ((array) $searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * Adds paging to the Log collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResourceModel\Log\Collection $collection
     * @return void
     */
    public function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection): void
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * Builds the search result of a Log collection
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param ResourceModel\Log\Collection $collection
     * @param SearchResultInterface $searchResults
     * @return mixed
     */
    public function buildSearchResult(
        SearchCriteriaInterface $searchCriteria,
        Collection $collection,
        SearchResultInterface $searchResults
    ): SearchResultInterface {
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
