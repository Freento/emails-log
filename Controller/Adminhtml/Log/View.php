<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Controller\Adminhtml\Log;

use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Model\Log;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Freento\EmailsLog\Model\LogRepository;

class View extends Action implements HttpGetActionInterface
{
    /**
     * @param ResultFactory $resultFactory
     * @param LogRepository $logRepository
     * @param DataPersistorInterface $dataPersistor
     * @param Context $context
     */
    public function __construct(
        protected $resultFactory,
        private readonly LogRepository $logRepository,
        private readonly DataPersistorInterface $dataPersistor,
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Renders log view page
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/');
        $logId = $this->getRequest()->getParam(LogInterface::LOG_ID);

        if ($logId === '') {
            return $resultRedirect;
        }

        try {
            $emailLog = $this->logRepository->getById($logId);
            $this->dataPersistor->set(Log::DATA_PERSISTOR_KEY, $emailLog);
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->prepend(__('Email log %1', $emailLog->getLogId()));
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('Can not find email log record with ID %1', $logId));
            return $resultRedirect;
        }

        return $resultPage;
    }
}
