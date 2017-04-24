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


namespace Mirasvit\Rewards\Pricing\Render;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\AbstractAdjustment;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Catalog\Helper\Data as CatalogData;
use Magento\Weee\Helper\Data as WeeData;

use Mirasvit\Rewards\Model\Config;
use Mirasvit\Rewards\Helper\Balance\Spend;
use \Mirasvit\Rewards\Helper\Balance\Earn;
use Mirasvit\Rewards\Helper\Data;

/**
 * @method string getIdSuffix()
 * @method string getDisplayLabel()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Adjustment extends AbstractAdjustment
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Config                 $config,
        Earn                   $earnHelper,
        Spend                  $spendHelper,
        Data                   $rewardsDataHelper,
        CatalogData            $catalogData,
        WeeData                $weeData,
        Session                $customerSession,
        PriceCurrencyInterface $priceCurrency,
        Template\Context       $context,
        array                  $data = []
    ) {
        $this->earnHelper        = $earnHelper;
        $this->spendHelper       = $spendHelper;
        $this->rewardsDataHelper = $rewardsDataHelper;
        $this->catalogData       = $catalogData;
        $this->weeData           = $weeData;
        $this->customerSession   = $customerSession;
        $this->config            = $config;

        parent::__construct($context, $priceCurrency, $data);
    }

    /**
     * @return string
     */
    protected function apply()
    {
        return $this->toHtml();
    }

    /**
     * Obtain code of adjustment type
     *
     * @return string
     */
    public function getAdjustmentCode()
    {
        return \Mirasvit\Rewards\Pricing\Adjustment::ADJUSTMENT_CODE;
    }

    /**
     * Define if both prices should be displayed
     *
     * @return bool
     */
    public function isShowPoints()
    {
        if ($this->isProductPage()) {
            $isAllowToShow = $this->config->getDisplayOptionsIsShowPointsOnProductPage();
        } else {
            $isAllowToShow = $this->config->getDisplayOptionsIsShowPointsOnFrontend();
        }

        return $isAllowToShow && $this->getPointsFormatted();
    }

    /**
     * @return bool
     */
    public function isIncludeTax()
    {
        return (bool)$this->config->getGeneralIsIncludeTaxEarning();
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        $price = null;
        if ($this->getData('price_type')) {
            $priceType = 'get' . ucfirst($this->getData('price_type'));
            $price = $this->getSaleableItem()->$priceType();
            if (
                !$price &&
                $priceType == 'getMinPrice' &&
                $this->getSaleableItem()->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE
            ) {
                return $price;
            }
            $price = $this->catalogData->getTaxPrice($this->getSaleableItem(), $price, $this->isIncludeTax());
            if ($this->isIncludeTax()) {
                $price += $this->weeData->getAmountExclTax($this->getSaleableItem());
            }
        }
        $productPoints = $this->earnHelper->getProductPoints(
            $this->getSaleableItem(),
            $this->customerSession->getCustomerGroupId(),
            $this->_storeManager->getStore()->getWebsiteId(),
            false,
            $price
        );

        return $productPoints;
    }

    /**
     * @return string|bool
     */
    public function getPointsFormatted()
    {
        $websiteId       = $this->_storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomer()->getGroupId();

        $points = $this->getPoints();
        if (!$points) {
            return false;
        }
        $money = $this->spendHelper->getProductPointsAsMoney($points, $websiteId, $customerGroupId);
        if ($points != $money) {
            return __('Possible discount '.$this->getLabel().' %1', $money);
        }

        $label = __('Earn '.$this->getLabel().' %1', $this->rewardsDataHelper->formatPoints($points));
        if (
            $this->getSaleableItem()->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE &&
            $this->isProductPage()
        ) {
            $label = __('Earn up to '.$this->getLabel().' %1', $this->rewardsDataHelper->formatPoints($points));
        }

        return $label;
    }

    /**
     * Build identifier with prefix
     *
     * @param string $prefix
     * @return string
     */
    public function buildIdWithPrefix($prefix)
    {
        $priceId = $this->getPriceId();
        if (!$priceId) {
            $priceId = $this->getSaleableItem()->getId();
        }
        return $prefix . $priceId . $this->getIdSuffix();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        $label = '';
        if (!$this->getAmountRender()) {
            return $label;
        }

        switch ($this->getAmountRender()->getTypeId()) {
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                $label = 'starting at';
                break;
            case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
                $label = 'Up to';
                break;
        }

        return $label;
    }

    /**
     * @return bool
     */
    private function isProductPage()
    {
        return $this->getData('zone') == \Magento\Framework\Pricing\Render::ZONE_ITEM_VIEW;
    }
}
