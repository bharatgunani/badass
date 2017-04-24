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



namespace Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
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
     * @param \Magento\Backend\Block\Widget\Context    $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
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
        $this->setId('notification_rule_tabs');
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
                ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit\Tab\General')->toHtml(),
        ]);
        $this->addTab('conditions_section', [
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'content' => $this->getLayout()
                ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit\Tab\Conditions')->toHtml(),
        ]);
        $this->addTab('action_section', [
            'label' => __('Actions'),
            'title' => __('Actions'),
            'content' => $this->getLayout()
                ->createBlock('\Mirasvit\Rewards\Block\Adminhtml\Notification\Rule\Edit\Tab\Action')->toHtml(),
        ]);

        return parent::_beforeToHtml();
    }

    /************************/
}
