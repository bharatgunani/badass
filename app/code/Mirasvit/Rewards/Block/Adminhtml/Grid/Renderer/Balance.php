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



namespace Mirasvit\Rewards\Block\Adminhtml\Grid\Renderer;

//class Balance extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Extended
class Balance extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $this->_value = $this->_getValue($row);
        $this->_text = parent::render($row);

        $color = '';
        if ($this->_value > 0) {
            $color = 'green';
            $this->_text = '+' . $this->_text;
        } elseif ($this->_value < 0) {
            $color = 'red';
        }

        return "<span class='$color'>$this->_text</span>";
    }
}