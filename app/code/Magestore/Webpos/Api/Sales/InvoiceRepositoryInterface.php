<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */
namespace Magestore\Webpos\Api\Sales;

interface InvoiceRepositoryInterface extends \Magento\Sales\Api\InvoiceRepositoryInterface
{
    /**
     * Performs persist operations for a specified invoice.
     *
     * @param \Magento\Sales\Api\Data\InvoiceInterface $entity The invoice.
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface|null $payment
     * @param string|null $invoiceAmount
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface Order interface.
     */
    public function saveInvoice(
        \Magento\Sales\Api\Data\InvoiceInterface $entity,
        \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment = null,
        $invoiceAmount = null
    );
}
