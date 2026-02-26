<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Plugin\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Rollpix\DisableSales\Model\Config;

class CartItemRepositoryPlugin
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
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
        if ($this->config->isEnabled()) {
            throw new LocalizedException(__(strip_tags($this->config->getMessage())));
        }

        return [$cartItem];
    }
}
