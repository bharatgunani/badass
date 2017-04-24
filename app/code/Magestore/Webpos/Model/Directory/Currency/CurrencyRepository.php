<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Model\Directory\Currency;
/**
 * class \Magestore\Webpos\Model\Currency\CurrencyRepository
 *
 * Methods:
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class CurrencyRepository implements \Magestore\Webpos\Api\Directory\Currency\CurrencyRepositoryInterface
{
    /**
     * webpos currency model
     *
     * @var \Magestore\Webpos\Model\Directory\Currency\Currency
     */
    protected $_currencyModel;

    /**
     * webpos currency result interface
     *
     * @var \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyResultInterfaceFactory
     */
    protected $_currencyResultInterface;

    /**
     * @param \Magestore\Webpos\Model\Directory\Currency\Currency $currencyModel
     * @param \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyResultInterfaceFactory $currencyResultInterface
     */
    public function __construct(
        \Magestore\Webpos\Model\Directory\Currency\Currency $currencyModel,
        \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyResultInterfaceFactory $currencyResultInterface
    ) {
        $this->_currencyModel= $currencyModel;
        $this->_currencyResultInterface = $currencyResultInterface;
    }

    /**
     * Get currencies list
     *
     * @api
     * @return array|null
     */
    public function getList() {
        $currencyList = $this->_currencyModel->getCurrencyList();
        $currencies = $this->_currencyResultInterface->create();
        $currencies->setItems($currencyList);
        $currencies->setTotalCount(count($currencyList));
        return $currencies;
    }
}