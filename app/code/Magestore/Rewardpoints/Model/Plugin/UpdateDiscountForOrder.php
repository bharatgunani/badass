<?php
namespace Magestore\Rewardpoints\Model\Plugin;
/**
 * Class UpdateDiscountForOrder
 * @package Magestore\Rewardpoints\Model\Plugin
 */
class UpdateDiscountForOrder
{

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
    /**
     *
     */
    const AMOUNT_Payment = 'payment_fee';
    /**
     *
     */
    const AMOUNT_SUBTOTAL = 'subtotal';

    /**
     * UpdateDiscountForOrder constructor.
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Quote\Model\Quote $quote,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->quote = $quote;
        $this->logger = $logger;
        $this->_checkoutSession = $checkoutSession;
        $this->_registry = $registry;
    }

    /**
     * @param $cart
     * @param $result
     * @return mixed
     */
    public function afterGetAmounts($cart, $result)
    {
        $total = $result;
        $quote = $this->_checkoutSession->getQuote();
        $paymentMethod = $quote->getPayment()->getMethod();
        $paypalMehodList = ['payflowpro','payflow_link','payflow_advanced','braintree_paypal','paypal_express_bml','payflow_express_bml','payflow_express','paypal_express'];

        if(in_array($paymentMethod,$paypalMehodList)){
            $total[self::AMOUNT_SUBTOTAL] = $total[self::AMOUNT_SUBTOTAL] - $quote->getRewardpointsDiscount();

        }

        return  $total;
    }

    /**
     * @param $cart
     */
    public function beforeGetAllItems($cart)
    {
        $paypalTest = $this->_registry->registry('is_paypal_items')? $this->_registry->registry('is_paypal_items') : 0;
        $quote = $this->_checkoutSession->getQuote();
        $paymentMethod = $quote->getPayment()->getMethod();

        $paypalMehodList = ['payflowpro','payflow_link','payflow_advanced','braintree_paypal','paypal_express_bml','payflow_express_bml','payflow_express','paypal_express'];
        if($paypalTest < 3 && in_array($paymentMethod,$paypalMehodList)){
            if(method_exists($cart , 'addCustomItem' ))
            {
                $cart->addCustomItem(__("Reward Point Discount"), 1 ,  -1.00 * $quote->getRewardpointsDiscount());
                $reg = $this->_registry->registry('is_paypal_items');
                $current = $reg + 1 ;
                $this->_registry->unregister('is_paypal_items');
                $this->_registry->register('is_paypal_items', $current);
            }
        }
    }
}