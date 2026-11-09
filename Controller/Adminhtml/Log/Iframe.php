<?php

declare(strict_types=1);

namespace Freento\EmailsLog\Controller\Adminhtml\Log;

use Freento\EmailsLog\Api\Data\LogInterface;
use Freento\EmailsLog\Model\LogRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Iframe extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    public const ADMIN_RESOURCE = 'Freento_EmailsLog::freento_log';

    /**
     * Sandbox flags for the email content, shared with the iframe "sandbox" attribute
     *
     * "allow-same-origin" lets web fonts from the email CSS load without CORS headers on the static server.
     * Never add "allow-scripts" here: together with "allow-same-origin" it removes the sandbox completely.
     */
    public const SANDBOX_FLAGS = 'allow-same-origin allow-popups allow-popups-to-escape-sandbox';

    /**
     * @param Context $context
     * @param LogRepository $logRepository
     */
    public function __construct(
        Context $context,
        private readonly LogRepository $logRepository
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

        /** @var Raw $resultRaw */
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        try {
            $resultFrame = $resultRaw->setContents(
                $this->logRepository->getById($logId)->getContent()
            );
        } catch (NoSuchEntityException) {
            $this->messageManager->addErrorMessage(__('Cannot find email log with ID %1', $logId));
            return $resultRedirect;
        }

        // Email HTML is untrusted: render it without scripts and forms, also when this URL is opened directly.
        // Only "sandbox" is set, because in the restrict mode Magento_Csp prepends its own policy
        // to this header and repeated directives would be ignored.
        // The flags must not be empty: Laminas turns an empty "sandbox" into the invalid "sandbox 'none'".
        $resultFrame->setHeader('Content-Security-Policy', 'sandbox ' . self::SANDBOX_FLAGS, true);
        $resultFrame->setHeader('X-Content-Type-Options', 'nosniff', true);
        $resultFrame->setHeader('Content-Type', 'text/html; charset=UTF-8', true);

        return $resultFrame;
    }
}
