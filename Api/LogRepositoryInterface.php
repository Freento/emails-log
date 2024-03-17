<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Api;

use Exception;
use Freento\EmailsLog\Api\Data\LogInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\SearchResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

interface LogRepositoryInterface
{
    /**
     * Saves an email log object to database
     *
     * @param LogInterface $log
     * @return LogInterface
     * @throws AlreadyExistsException
     */
    public function save(LogInterface $log): LogInterface;

    /**
     * Deletes an email log from a database by Log object
     *
     * @param LogInterface $log
     * @return void
     * @throws Exception
     */
    public function delete(LogInterface $log): void;

    /**
     * Returns a list of email logs from a database by $searchCriteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultInterface;

    /**
     * Returns a log from database by ID
     *
     * @param string $logId
     * @return LogInterface
     * @throws NoSuchEntityException
     */
    public function getById(string $logId): LogInterface;
}
