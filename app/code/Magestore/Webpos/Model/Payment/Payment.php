<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Payment;
use Magestore\Webpos\Api\Data\Payment\PaymentInterface;

/**
 * Class Magestore\Webpos\Model\Payment\Payment
 *
 */
class Payment extends \Magento\Framework\Model\AbstractModel implements
    \Magestore\Webpos\Api\Data\Payment\PaymentInterface
{
    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get title
     *
     * @api
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }


    /**
     * Set type
     *
     * @api
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get type
     *
     * @api
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set information
     *
     * @api
     * @param string $information
     * @return $this
     */
    public function setInformation($information)
    {
        return $this->setData(self::INFORMATION, $information);
    }

    /**
     * Get information
     *
     * @api
     * @return string|null
     */
    public function getInformation()
    {
        return $this->getData(self::INFORMATION);
    }

    /**
     * Get icon class
     *
     * @api
     * @return string|null
     */
    public function getIconClass()
    {
        return $this->getData(self::ICON_CLASS);
    }

    /**
     * Set icon class
     *
     * @api
     * @param string $iconClass
     * @return $this
     */
    public function setIconClass($iconClass)
    {
        return $this->setData(self::ICON_CLASS, $iconClass);
    }

    /**
     * Get is default
     *
     * @api
     * @return string|null
     */
    public function getIsDefault()
    {
        return $this->getData(self::IS_DEFAULT);
    }

    /**
     * Set is default
     *
     * @api
     * @param string $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault)
    {
        return $this->setData(self::IS_DEFAULT, $isDefault);
    }

    /**
     * Get is pay later
     *
     * @api
     * @return string|null
     */
    public function getIsPayLater()
    {
        return $this->getData(self::IS_PAY_LATER);
    }

    /**
     * Set is pay later
     *
     * @api
     * @param string $isPayLater
     * @return $this
     */
    public function setIsPayLater($isPayLater)
    {
        return $this->setData(self::IS_PAY_LATER, $isPayLater);
    }

    /**
     * Get is reference number
     *
     * @api
     * @return string|null
     */
    public function getIsReferenceNumber()
    {
        return $this->getData(self::IS_REFERENCE_NUMBER);
    }

    /**
     * Set is reference number
     *
     * @api
     * @param string $isReferenceNumber
     * @return $this
     */
    public function setIsReferenceNumber($isReferenceNumber)
    {
        return $this->setData(self::IS_REFERENCE_NUMBER, $isReferenceNumber);
    }

    /**
     * Get multiable
     *
     * @api
     * @return string|null
     */
    public function getMultiable()
    {
        return $this->getData(self::MULTIABLE);
    }

    /**
     * Set multiable
     *
     * @api
     * @param string $multiable
     * @return $this
     */
    public function setMultiable($multiable)
    {
        return $this->setData(self::MULTIABLE, $multiable);
    }


}
