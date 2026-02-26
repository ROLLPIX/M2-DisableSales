<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Plugin\Cart;

use Magento\Checkout\Controller\Cart\Add;
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

    public function __construct(
        Config $config,
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        JsonFactory $jsonFactory
    ) {
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Block add-to-cart action when sales are disabled.
     */
    public function aroundExecute(Add $subject, callable $proceed): ResultInterface
    {
        if (!$this->config->isEnabled()) {
            return $proceed();
        }

        $plainMessage = strip_tags($this->config->getMessage());

        $request = $subject->getRequest();
        if ($request->isAjax() || $request->getParam('is_ajax')) {
            $result = $this->jsonFactory->create();
            return $result->setData([
                'success' => false,
                'error' => true,
                'error_message' => $plainMessage,
                'messages' => $plainMessage,
            ]);
        }

        $this->messageManager->addErrorMessage($plainMessage);

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $subject->getResultRedirectFactory()->create();
        return $resultRedirect->setUrl($this->redirect->getRefererUrl());
    }
}
