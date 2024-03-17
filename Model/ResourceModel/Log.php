<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Model\ResourceModel;

use Freento\EmailsLog\Api\Data\LogInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('emailslog_log', LogInterface::LOG_ID);
    }
}
