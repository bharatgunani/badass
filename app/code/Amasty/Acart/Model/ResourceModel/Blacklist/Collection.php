<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

namespace Amasty\Acart\Model\ResourceModel\Blacklist;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Acart\Model\Blacklist', 'Amasty\Acart\Model\ResourceModel\Blacklist');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}