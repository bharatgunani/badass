<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Payment\Online\Authorizenet;

/**
 * class \Magestore\Webpos\Model\Payment\Online\Authorizenet\Directpost
 *
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Directpost extends \Magento\Authorizenet\Observer\AddFieldsToResponseObserver
{

    /**
     * get authorize request information
     *
     * @param  $order
     * @return array
     */
    public function getRequestInformation($order)
    {
        if (!$order || !$order->getId()) {
            return false;
        }
        $payment = $order->getPayment();
        if (!$payment || $payment->getMethod() != $this->payment->getCode()) {
            return $this;
        }
        $result = array();
        $this->session->addCheckoutOrderIncrementId($order->getIncrementId());
        $this->session->setLastOrderIncrementId($order->getIncrementId());
        $requestToAuthorizenet = $payment->getMethodInstance()
            ->generateRequestFromOrder($order);
        $requestToAuthorizenet->setControllerActionName('directpost_payment');
        $requestToAuthorizenet->setIsSecure(
            (string)$this->storeManager->getStore()
                ->isCurrentlySecure()
        );
        $result['url'] = 'https://test.authorize.net/gateway/transact.dll';
        $result['params'] = $requestToAuthorizenet->getData();
        $result['params'] = $requestToAuthorizenet->getData();
        return $result;
    }

}