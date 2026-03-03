<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Plugin\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Rollpix\DisableSales\Model\Config;

class CartItemRepositoryPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    public function __construct(
        Config $config,
        CartRepositoryInterface $cartRepository
    ) {
        $this->config = $config;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Block add-to-cart via REST API / GraphQL.
     *
     * @throws LocalizedException
     */
    public function beforeSave(
        CartItemRepositoryInterface $subject,
        CartItemInterface $cartItem
    ): array {
        if (!$this->config->isEnabled()) {
            return [$cartItem];
        }

        $customerGroupId = 0;
        $quoteId = $cartItem->getQuoteId();

        if ($quoteId) {
            try {
                $quote = $this->cartRepository->get($quoteId);
                $customerGroupId = (int) $quote->getCustomerGroupId();
            } catch (NoSuchEntityException $e) {
                // Quote not found - treat as guest (group 0)
            }
        }

        if ($this->config->isSalesDisabledForGroup($customerGroupId)) {
            throw new LocalizedException(__(strip_tags($this->config->getMessage())));
        }

        return [$cartItem];
    }
}
