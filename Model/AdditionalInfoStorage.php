<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Model;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;

class AdditionalInfoStorage
{
    /**
     * @var DataObject[]
     */
    private array $storage = [];

    /**
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(private readonly DataObjectFactory $dataObjectFactory)
    {
    }

    /**
     * Set data value
     *
     * @param string $id
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setDataValue(string $id, string $key, string $value): void
    {
        $data = $this->getData($id);
        $data->setData($key, $value);
    }

    /**
     * Get data
     *
     * @param string $id
     * @return DataObject
     */
    public function getData(string $id): DataObject
    {
        if (!isset($this->storage[$id])) {
            $this->storage[$id] = $this->dataObjectFactory->create();
        }

        return $this->storage[$id];
    }
}
