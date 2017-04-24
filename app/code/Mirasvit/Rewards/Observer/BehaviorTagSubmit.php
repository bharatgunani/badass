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

class BehaviorTagSubmit implements ObserverInterface
{
    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @param \Mirasvit\Rewards\Helper\Data     $rewardsData
     * @param \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior
    ) {
        $this->rewardsData = $rewardsData;
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
        $tag = $observer->getEvent()->getObject();
        if (($tag->getApprovedStatus() == $tag->getStatus()) && $tag->getFirstCustomerId()) {
            $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_TAG,
                $tag->getFirstCustomerId(), $this->rewardsData->getWebsiteId($tag->getFirstStoreId()), $tag->getId());
        }
    }
}
