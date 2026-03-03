<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Plugin\Cart;

use Magento\Checkout\Controller\Cart\Add;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Rollpix\DisableSales\Model\Config;

class AddPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        Config $config,
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        JsonFactory $jsonFactory,
        CustomerSession $customerSession
    ) {
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->jsonFactory = $jsonFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Block add-to-cart action when sales are disabled.
     */
    public function aroundExecute(Add $subject, callable $proceed): ResultInterface
    {
        if (!$this->config->isEnabled()) {
            return $proceed();
        }

        $customerGroupId = (int) $this->customerSession->getCustomerGroupId();

        if (!$this->config->isSalesDisabledForGroup($customerGroupId)) {
            return $proceed();
        }

        $plainMessage = strip_tags($this->config->getMessage());

        $request = $subject->getRequest();
        if ($request->isAjax() || $request->getParam('is_ajax')) {
            $result = $this->jsonFactory->create();
            $result->setHttpResponseCode(400);
            return $result->setData([
                'message' => $plainMessage,
                'rollpix_sales_disabled' => true,
            ]);
        }

        $this->messageManager->addErrorMessage($plainMessage);

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $subject->getResultRedirectFactory()->create();
        return $resultRedirect->setUrl($this->redirect->getRefererUrl());
    }
}
