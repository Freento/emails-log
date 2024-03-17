<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public const STATUS_SUCCESS = 'Success';
    public const STATUS_FAILED = 'Failed';

    /**
     * Returns array of options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['label' => __('Success'), 'value' => self::STATUS_SUCCESS],
            ['label' => __('Failed'), 'value' => self::STATUS_FAILED]
        ];
    }
}
