<?php

declare(strict_types=1);

namespace Rollpix\DisableSales\Plugin\CustomerData;

use Magento\Customer\CustomerData\Customer;
use Magento\Customer\Model\Session\Proxy as CustomerSession;

class CustomerPlugin
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * Add group_id to customer section data so frontend JS can check it.
     */
    public function afterGetSectionData(Customer $subject, array $result): array
    {
        if ($this->customerSession->isLoggedIn()) {
            $result['group_id'] = $this->customerSession->getCustomerGroupId();
        }

        return $result;
    }
}
