<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Plugin\Mail\Template;

use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Model\AdditionalInfoStorage;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;

class TransportBuilderPlugin
{
    /**
     * @param AdditionalInfoStorage $additionalInfoStorage
     */
    public function __construct(
        private readonly AdditionalInfoStorage $additionalInfoStorage
    ) {
    }

    /**
     * Save template to additional info storage
     *
     * @param TransportBuilder $subject
     * @param string $templateIdentifier
     * @return void
     */
    public function beforeSetTemplateIdentifier(TransportBuilder $subject, $templateIdentifier): void
    {
        $id = spl_object_hash($subject);
        $this->additionalInfoStorage->setDataValue($id, LogInterface::TEMPLATE, (string)$templateIdentifier);
    }

    /**
     * Save store to additional info storage
     *
     * @param TransportBuilder $subject
     * @param array $templateOptions
     * @return void
     */
    public function beforeSetTemplateOptions(TransportBuilder $subject, $templateOptions)
    {
        $id = spl_object_hash($subject);
        $store = $templateOptions['store'] ?? '';
        $this->additionalInfoStorage->setDataValue($id, LogInterface::STORE_ID, (string)$store);
    }

    /**
     * Save template and store for transport object
     *
     * @param TransportBuilder $subject
     * @param TransportInterface $result
     * @return TransportInterface
     */
    public function afterGetTransport(TransportBuilder $subject, TransportInterface $result)
    {
        $builderId = spl_object_hash($subject);
        $transportId = spl_object_hash($result);

        $data = $this->additionalInfoStorage->getData($builderId);
        $this->additionalInfoStorage->setDataValue(
            $transportId,
            LogInterface::TEMPLATE,
            $data->getData(LogInterface::TEMPLATE)
        );
        $this->additionalInfoStorage->setDataValue(
            $transportId,
            LogInterface::STORE_ID,
            $data->getData(LogInterface::STORE_ID)
        );

        return $result;
    }
}
