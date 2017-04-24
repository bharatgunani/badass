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



namespace Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @param \Magento\Framework\Registry              $registry
     * @param \Magento\Backend\Block\Widget\Context    $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->context = $context;
        $this->jsonEncoder = $jsonEncoder;
        $this->authSession = $authSession;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('earning_rule_tabs');
        $this->setDestElementId('edit_form');
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', [
            'label' => __('General Information'),
            'title' => __('General Information'),
            'content' => $this->getLayout()
                ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab\General')->toHtml(),
        ]);
        $earningRule = $this->registry->registry('current_earning_rule');
        if (!$earningRule->getType()) {
            return parent::_beforeToHtml();
        }
        switch ($earningRule->getType()) {
            case \Mirasvit\Rewards\Model\Earning\Rule::TYPE_PRODUCT:
                $this->addTab('product_section', [
                    'label' => __('Conditions & Actions'),
                    'title' => __('Conditions & Actions'),
                    'content' => $this->getLayout()
                        ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab\Product')->toHtml(),
                ]);
                $this->addTab('product_notification', [
                    'label' => __('Notification'),
                    'title' => __('Notification'),
                    'content' => $this->getLayout()
                        ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab\ProductNotification')
                        ->toHtml(),
                ]);
                break;
            case \Mirasvit\Rewards\Model\Earning\Rule::TYPE_CART:
                $this->addTab('conditions_section', [
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                    'content' => $this->getLayout()
                        ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab\Conditions')->toHtml(),
                ]);
                $this->addTab('cart_section', [
                    'label' => __('Actions'),
                    'title' => __('Actions'),
                    'content' => $this->getLayout()
                        ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab\Cart')->toHtml(),
                ]);
                break;
            case \Mirasvit\Rewards\Model\Earning\Rule::TYPE_BEHAVIOR:
                $this->addTab('behavior_section', [
                    'label' => __('Conditions & Actions'),
                    'title' => __('Conditions & Actions'),
                    'content' => $this->getLayout()
                        ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab\Behavior')->toHtml(),
                ]);
                $this->addTab('notification_section', [
                    'label' => __('Notifications'),
                    'title' => __('Notifications'),
                    'content' => $this->getLayout()
                        ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Earning\Rule\Edit\Tab\Notification')->toHtml(),
                ]);
                break;
        }

        return parent::_beforeToHtml();
    }

    /************************/
}
