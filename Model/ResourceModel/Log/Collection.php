<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Model\ResourceModel\Log;

use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Model\Log;
use Freento\EmailsLog\Model\ResourceModel\Log as LogResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = LogInterface::LOG_ID;

    /**
     * @var string
     */
    protected $_eventPrefix = 'emailslog_log_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'log_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Log::class, LogResourceModel::class);
    }
}
