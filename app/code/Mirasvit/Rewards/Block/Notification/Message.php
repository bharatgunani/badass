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



namespace Mirasvit\Rewards\Block\Notification;

use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

class Message extends \Magento\Framework\View\Element\Messages
{
    public function __construct(
        \Mirasvit\Rewards\Helper\Message $messageHelper,
        \Mirasvit\Rewards\Helper\Balance\Earn $rewardsBalanceEarn,
        \Mirasvit\Rewards\Helper\Rule\Notification $rewardsPurchase,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Framework\Message\CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        InterpretationStrategyInterface $interpretationStrategy,
        array $data = []
    ) {
        $this->messageHelper      = $messageHelper;
        $this->rewardsBalanceEarn = $rewardsBalanceEarn;
        $this->rewardsPurchase    = $rewardsPurchase;
        $this->context            = $context;
        $this->messageManager     = $messageManager;

        parent::__construct(
            $context,
            $messageFactory,
            $collectionFactory,
            $messageManager,
            $interpretationStrategy,
            $data
        );
    }

    /**
     * @return int
     */
    public function getProductPoints()
    {
        return $this->rewardsBalanceEarn->getProductPoints($this->getProduct());
    }

    /**
     * @var bool|array
     */
    protected $rules = false;

    /**
     * @return array|bool
     */
    public function getRules()
    {
        if (!$this->rules) {
            $this->rules = $this->rewardsPurchase->calcNotificationRules();
        }

        return $this->rules;
    }

    /**
     * @param \Mirasvit\Rewards\Model\Notification\Rule $rule
     *
     * @return string
     */
    public function getMessage($rule)
    {
        return $this->messageHelper->processNotificationVariables($rule->getMessage());
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        /** @var \Mirasvit\Rewards\Model\Notification\Rule $rule */
        foreach ($this->getRules() as $rule) {
            $message = $this->messageHelper->processNotificationVariables($rule->getMessage());
            $this->messageManager->addSuccess($message);
        }
        $this->addMessages($this->messageManager->getMessages(true));

        return parent::_prepareLayout();
    }
}
