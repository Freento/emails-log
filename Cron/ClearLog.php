<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Cron;

use Exception;
use Freento\EmailsLog\Model\ClearOutdatedLogs;
use Psr\Log\LoggerInterface;

class ClearLog
{
    /**
     * @param ClearOutdatedLogs $clearOutdatedLogs
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ClearOutdatedLogs $clearOutdatedLogs,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Delete logs from a database if their $dayBeforeClean passed
     *
     * @return void
     */
    public function execute(): void
    {
        try {
            $this->clearOutdatedLogs->execute();
        } catch (Exception $e) {
            $this->logger->critical(
                'Freento_EmailsLog: Error while deleting outdated logs: '
                . $e->getMessage()
            );
        }
    }
}
