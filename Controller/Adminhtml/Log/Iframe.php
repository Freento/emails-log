<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Controller\Adminhtml\Log;

use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Model\LogRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Iframe extends Action implements HttpGetActionInterface
{
    /**
     * @param Context $context
     * @param LogRepository $logRepository
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        Context $context,
        private readonly LogRepository $logRepository,
        protected $resultFactory
    ) {
        parent::__construct($context);
    }

    /**
     * Displays a log content in iframe tags
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

        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        try {
            $resultFrame = $resultRaw->setContents(
                $this->logRepository->getById($logId)->getContent()
            );
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('Cannot find email log with ID %1', $logId));
            return $resultRedirect;
        }

        return $resultFrame;
    }
}
