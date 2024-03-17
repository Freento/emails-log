<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Controller\Adminhtml\Log;

use Exception;
use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Api\LogRepositoryInterface;
use Freento\EmailsLog\Model\ResourceModel\Log\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param LogRepositoryInterface $logRepository
     * @param LoggerInterface $logger
     * @param Context $context
     */
    public function __construct(
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
        private readonly LogRepositoryInterface $logRepository,
        private readonly LoggerInterface $logger,
        Context $context,
    ) {
        parent::__construct($context);
    }

    /**
     * Deletes selected logs from Database
     *
     * @return ResultInterface
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        try {
            /** @var LogInterface $log */
            foreach ($collection->getItems() as $log) {
                $this->logRepository->delete($log);
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__(
                'An error occurred while deleting email log(s). See log files for details'
            ));
            $this->logger->error('Freento_EmailsLog: Error while deleting email logs: ' . $e->getMessage());

            return $resultRedirect;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );

        return $resultRedirect;
    }
}
