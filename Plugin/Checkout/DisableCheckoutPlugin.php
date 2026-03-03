<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Plugin\Checkout;

use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Rollpix\DisableSales\Model\Config;

class DisableCheckoutPlugin
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
     * @var RedirectFactory
     */
    private $redirectFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        Config $config,
        ManagerInterface $messageManager,
        RedirectFactory $redirectFactory,
        UrlInterface $url,
        CustomerSession $customerSession
    ) {
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
        $this->url = $url;
        $this->customerSession = $customerSession;
    }

    /**
     * Redirect to cart if checkout is disabled.
     */
    public function aroundExecute(ActionInterface $subject, callable $proceed): ResultInterface
    {
        if (!$this->config->isEnabled() || !$this->config->isCheckoutDisabled()) {
            return $proceed();
        }

        $customerGroupId = (int) $this->customerSession->getCustomerGroupId();

        if (!$this->config->isSalesDisabledForGroup($customerGroupId)) {
            return $proceed();
        }

        $this->messageManager->addErrorMessage(strip_tags($this->config->getMessage()));

        $resultRedirect = $this->redirectFactory->create();
        return $resultRedirect->setUrl($this->url->getUrl('checkout/cart'));
    }
}
