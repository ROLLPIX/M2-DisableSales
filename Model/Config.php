<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const PREFIX = 'rollpix_disablesales/';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    // --- General ---

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PREFIX . 'general/enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getMessage(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::PREFIX . 'general/message',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isCheckoutDisabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PREFIX . 'general/disable_checkout',
            ScopeInterface::SCOPE_STORE
        );
    }

    // --- Banner ---

    public function isBannerEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PREFIX . 'banner/show_banner',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getBannerBgColor(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::PREFIX . 'banner/banner_bg_color',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getBannerTextColor(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::PREFIX . 'banner/banner_text_color',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getBannerCustomCss(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::PREFIX . 'banner/banner_custom_css',
            ScopeInterface::SCOPE_STORE
        );
    }

    // --- Modal ---

    public function isModalEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PREFIX . 'modal/show_modal',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getModalBgColor(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::PREFIX . 'modal/modal_bg_color',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getModalTextColor(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::PREFIX . 'modal/modal_text_color',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getModalCustomCss(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::PREFIX . 'modal/modal_custom_css',
            ScopeInterface::SCOPE_STORE
        );
    }
}
