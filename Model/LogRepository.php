<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Model;

use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Api\Data\LogSearchResultsInterfaceFactory;
use Freento\EmailsLog\Api\LogRepositoryInterface;
use Freento\EmailsLog\Model\ResourceModel\Log\CollectionFactory;
use Freento\EmailsLog\Model\ResourceModel\Log;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\SearchResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class LogRepository extends AbstractRepository implements LogRepositoryInterface
{
    /**
     * @param Log $logResource
     * @param LogFactory $logFactory
     * @param CollectionFactory $logCollectionFactory
     * @param LogSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        private readonly Log $logResource,
        private readonly LogFactory $logFactory,
        private readonly CollectionFactory $logCollectionFactory,
        private readonly LogSearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    /**
     * @inheritDoc
     */
    public function save(LogInterface $log): LogInterface
    {
        $this->logResource->save($log);

        return $log;
    }

    /**
     * @inheritDoc
     */
    public function delete(LogInterface $log): void
    {
        $this->logResource->delete($log);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultInterface
    {
        $collection = $this->logCollectionFactory->create();
        $searchResults = $this->searchResultsFactory->create();
        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);
        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection, $searchResults);
    }

    /**
     * @inheritDoc
     */
    public function getById(string $logId): LogInterface
    {
        $getByIdModel = $this->logFactory->create();
        $this->logResource->load($getByIdModel, $logId);
        if (!$getByIdModel->getId()) {
            throw new NoSuchEntityException(__('The Email Log with the "%1" ID doesn\'t exist.', $logId));
        }

        return $getByIdModel;
    }
}
