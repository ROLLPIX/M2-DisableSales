<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rollpix\DisableSales\Model\Config;

class SalesStatus implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function isDisabled(): bool
    {
        return $this->config->isEnabled();
    }

    public function getMessage(): string
    {
        return $this->config->getMessage();
    }

    // --- Banner ---

    public function isBannerEnabled(): bool
    {
        return $this->config->isBannerEnabled();
    }

    public function getBannerBgColor(): string
    {
        return $this->config->getBannerBgColor();
    }

    public function getBannerTextColor(): string
    {
        return $this->config->getBannerTextColor();
    }

    public function getBannerCustomCss(): string
    {
        return $this->config->getBannerCustomCss();
    }

    // --- Modal ---

    public function isModalEnabled(): bool
    {
        return $this->config->isModalEnabled();
    }

    public function getModalBgColor(): string
    {
        return $this->config->getModalBgColor();
    }

    public function getModalTextColor(): string
    {
        return $this->config->getModalTextColor();
    }

    public function getModalCustomCss(): string
    {
        return $this->config->getModalCustomCss();
    }
}
