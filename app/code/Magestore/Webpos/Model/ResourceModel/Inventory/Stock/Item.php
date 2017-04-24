<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Model\ResourceModel\Inventory\Stock;

use \Magento\CatalogInventory\Model\Stock;

/**
 * Stock item resource model
 */
class Item extends \Magento\CatalogInventory\Model\ResourceModel\Stock\Item
{

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    public function addStockDataToCollection($collection)
    {
        //$websiteId = $this->_storeManager->getStore($collection->getStoreId())->getWebsiteId();
        $websiteId = 0;
        $joinCondition = $this->getConnection()->quoteInto(
            'stock_item_index.website_id = ? AND',
            $websiteId
        );
        
        $joinCondition = $this->getConnection()->quoteInto(
            'e.entity_id = stock_item_index.product_id',
            $websiteId
        );        

        $joinCondition .= $this->getConnection()->quoteInto(
            ' AND stock_item_index.stock_id = ?',
            Stock::DEFAULT_STOCK_ID
        );

        $collection->getSelect()->join(
            ['stock_item_index' => $this->getMainTable()],
            $joinCondition,
            ['item_id' => 'item_id',
                'stock_id' => 'stock_id',
                'product_id' => 'product_id',
                'qty' => 'qty',
                'is_in_stock' => 'is_in_stock',
                'manage_stock' => 'manage_stock',
                'use_config_manage_stock' => 'use_config_manage_stock',
                'backorders' => 'backorders',
                'use_config_backorders' => 'use_config_backorders',
                'min_sale_qty' => 'min_sale_qty',
                'use_config_min_sale_qty' => 'use_config_min_sale_qty',
                'max_sale_qty' => 'max_sale_qty',
                'use_config_max_sale_qty' => 'use_config_max_sale_qty',                
                'updated_time' => 'updated_time',                
            ]
        );

        return $collection;
    }
    
}