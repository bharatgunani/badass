<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   1.1.25
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Rewards\Model\Config;

class BehaviorCustomerAfterCommit implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Mirasvit\Rewards\Model\ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory
     */
    protected $referralCollectionFactory;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @param \Magento\Framework\Session\SessionManagerInterface               $session
     * @param \Mirasvit\Rewards\Model\ReferralFactory                          $referralFactory
     * @param \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory
     * @param \Mirasvit\Rewards\Helper\Behavior                                $rewardsBehavior
     */
    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Mirasvit\Rewards\Model\ReferralFactory $referralFactory,
        \Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory $referralCollectionFactory,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
    ) {
        $this->session = $session;
        $this->referralFactory = $referralFactory;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->rewardsBehavior = $rewardsBehavior;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (substr(php_sapi_name(), 0, 3) == 'cli') {
            return;
        }

        $customer = $observer->getEvent()->getCustomer();
        if (!$customer->_customerNew) {
            return;
        }

        $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_SIGNUP, $customer);
        $this->customerAfterCreate($customer);
    }

    /**
     * Customer sign up.
     *
     * @param \Magento\Customer\Model\Customer $customer
     *
     *
     * @return void
     */
    public function customerAfterCreate($customer)
    {
        $referral = false;
        if ($id = (int) $this->session->getReferral()) {
            /** @var \Mirasvit\Rewards\Model\Referral $referral */
            $referral = $this->referralFactory->create()->load($id);
        } else {
            $referrals = $this->referralCollectionFactory->create()
                ->addFieldToFilter('email', $customer->getEmail());
            if ($referrals->count()) {
                $referral = $referrals->getFirstItem();
            }
        }
        if (!$referral) {
            return;
        }
        /** @var \Mirasvit\Rewards\Model\Transaction $transaction */
        $transaction = $this->rewardsBehavior->processRule(
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP,
            $referral->getCustomerId(),
            false,
            $customer->getId()
        );
        $referral->finish(Config::REFERRAL_STATUS_SIGNUP, $customer->getId(), $transaction);
    }
}
