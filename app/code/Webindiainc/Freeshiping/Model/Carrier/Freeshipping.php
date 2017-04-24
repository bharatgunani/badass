<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Free shipping model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Webindiainc\Freeshiping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Freeshipping extends \Magento\OfflineShipping\Model\Carrier\Freeshipping {

    /**
     * @var string
     */
    protected $_code = 'freeshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * FreeShipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request) {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $this->_updateFreeMethodQuote($request);

        /* Custom Code By Webindia */
        $free_shipping_true = false;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
        $shippingAddress = $cart->getQuote()->getShippingAddress();
        $region = $shippingAddress->getData("region");
        /* Custom Code By Webindia */
        if ($region == "Alaska" || $region == "Hawaii") {
            $free_shipping_true = true;
            return false;
        }
        if ($request->getFreeShipping() || $request->getBaseSubtotalInclTax() >= $this->getConfigData(
                        'free_shipping_subtotal'
                )
        ) {



            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('freeshipping');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('freeshipping');
            $method->setMethodTitle($this->getConfigData('name'));
            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        }

        return $result;
    }

}
