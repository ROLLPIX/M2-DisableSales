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

    // --- Customer Groups ---

    public function hasCustomerGroupFilter(): bool
    {
        return $this->config->hasCustomerGroupFilter();
    }

    public function getRestrictedCustomerGroupsJson(): string
    {
        return json_encode($this->config->getRestrictedCustomerGroups());
    }

    // --- Banner ---

    public function isBannerEnabled(): bool
    {
        return $this->config->isBannerEnabled();
    }

    public function isBannerShowOnLogin(): bool
    {
        return $this->config->isBannerShowOnLogin();
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

    public function isModalShowOnLogin(): bool
    {
        return $this->config->isModalShowOnLogin();
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
