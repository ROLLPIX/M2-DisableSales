<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Plugin\Quote;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Rollpix\DisableSales\Model\Config;

class AddProductPlugin
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
     * Second layer: block product addition at quote level.
     *
     * @param Quote $subject
     * @param Product $product
     * @param float|null $request
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct(
        Quote $subject,
        Product $product,
        $request = null
    ): array {
        if ($this->config->isEnabled()) {
            throw new LocalizedException(__(strip_tags($this->config->getMessage())));
        }

        return [$product, $request];
    }
}
