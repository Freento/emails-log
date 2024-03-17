<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Model;

use DateTimeFactory;
use Exception;
use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Helper\Config;
use Freento\EmailsLog\Model\ResourceModel\Log\CollectionFactory;

class ClearOutdatedLogs
{
    /**
     * @param Config $helperConfig
     * @param LogRepository $logRepository
     * @param CollectionFactory $collectionFactory
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        private readonly Config $helperConfig,
        private readonly LogRepository $logRepository,
        private readonly CollectionFactory $collectionFactory,
        private readonly DateTimeFactory $dateTimeFactory
    ) {
    }

    /**
     * Delete logs from a database if their $dayBeforeClean passed
     *
     * @return void
     * @throws Exception
     */
    public function execute(): void
    {
        $daysBeforeClean = $this->helperConfig->getDaysBeforeClean();

        if ($daysBeforeClean > 0) {
            $timeEnd = $this->dateTimeFactory->create()
                ->modify('-' . $daysBeforeClean . ' days')
                ->format('Y-m-d H:i:s');

            $logs = $this->collectionFactory
                ->create()
                ->addFieldToFilter('created_at', ['lteq' => $timeEnd]);

            /** @var LogInterface $log */
            foreach ($logs as $log) {
                $this->logRepository->delete($log);
            }
        }
    }
}
