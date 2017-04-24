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



namespace Mirasvit\Rewards\Controller\Pinterest;

use Magento\Framework\Controller\ResultFactory;

class Pin extends \Mirasvit\Rewards\Controller\Pinterest
{
    /**
     * @return $this|string
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $response = '';
        $url = $this->getRequest()->getParam('url');
        $transaction = $this->rewardsBehavior->processRule(
            \Mirasvit\Rewards\Model\Config::BEHAVIOR_TRIGGER_PINTEREST_PIN,
            $this->_getCustomer(),
            false,
            $url
        );
        if ($transaction) {
            $resultJson = $this->resultJsonFactory->create();

            $response = $resultJson->setJsonData(
                __("You've earned %1 for Pin!", $this->rewardsData->formatPoints($transaction->getAmount()))
            );
        }

        return $response;
    }
}
