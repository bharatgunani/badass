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
use Magento\Framework\Message\ManagerInterface;

class AdminCustomerSaveAfter implements ObserverInterface
{
    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    protected $rewardsBalance;

    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Mirasvit\Rewards\Helper\Balance $rewardsBalance
     * @param \Mirasvit\Rewards\Helper\Data    $rewardsData
     * @param ManagerInterface                 $messageManager
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Balance $rewardsBalance,
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        ManagerInterface $messageManager
    ) {
        $this->rewardsBalance = $rewardsBalance;
        $this->rewardsData = $rewardsData;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();
        $amount = (int) $request->getParam('rewards_change_balance');
        $message = $request->getParam('rewards_message');

        $emailMessage = '';
        if ($amount !== 0) {
            $amountBalace = $this->rewardsBalance->getBalancePoints($customer);
            if ($amountBalace + $amount < 0) {
                $amount = -$amountBalace;
            }

            if ($amount != 0) {
                $this->rewardsBalance->changePointsBalance(
                    $customer->getId(),
                    $amount, $message,
                    false,
                    true,
                    $emailMessage
                );
            }

            $formattedAmount = $this->rewardsData->formatPoints($amount);
            if ($amount > 0) {
                $alertMessage = __('%1 has been added.', $formattedAmount);
            } else {
                $alertMessage = __('%1 has been deducted.', $formattedAmount);
            }
            $this->messageManager->addSuccess($alertMessage);
        }
    }
}
