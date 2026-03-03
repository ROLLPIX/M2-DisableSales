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

    /**
     * Get the list of restricted customer group IDs.
     * Returns empty array when no groups are configured (means ALL groups).
     *
     * @return int[]
     */
    public function getRestrictedCustomerGroups(): array
    {
        $value = (string) $this->scopeConfig->getValue(
            self::PREFIX . 'general/customer_groups',
            ScopeInterface::SCOPE_STORE
        );
        if ($value === '') {
            return [];
        }
        return array_map('intval', explode(',', $value));
    }

    /**
     * Whether customer groups filtering is active (non-empty selection).
     */
    public function hasCustomerGroupFilter(): bool
    {
        return !empty($this->getRestrictedCustomerGroups());
    }

    /**
     * Check whether a specific customer group has sales disabled.
     * If no groups are configured, ALL groups are restricted (backward compat).
     */
    public function isSalesDisabledForGroup(int $groupId): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        $groups = $this->getRestrictedCustomerGroups();
        if (empty($groups)) {
            return true;
        }
        return in_array($groupId, $groups, true);
    }

    // --- Banner ---

    public function isBannerEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PREFIX . 'banner/show_banner',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isBannerShowOnLogin(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PREFIX . 'banner/show_on_login',
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

    public function isModalShowOnLogin(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::PREFIX . 'modal/show_on_login',
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
