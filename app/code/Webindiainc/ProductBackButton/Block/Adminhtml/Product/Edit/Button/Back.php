<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Webindiainc\ProductBackButton\Block\Adminhtml\Product\Edit\Button;

/**
 * Class Back
 */
class Back extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Back
{
    /**
     * @return array
     */
    public function getButtonData() {



        if (strpos($_SERVER['HTTP_REFERER'], 'productmanage') !== false) {
            return [
                'label' => __('Back'),
                'on_click' => sprintf("location.href = '%s';", $_SERVER['HTTP_REFERER']),
                'class' => 'back',
                'sort_order' => 10
            ];
        } else {
            return [
                'label' => __('Back'),
                'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/')),
                'class' => 'back',
                'sort_order' => 10
            ];
        }
    }
}
