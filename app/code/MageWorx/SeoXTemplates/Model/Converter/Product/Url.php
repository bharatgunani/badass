<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoXTemplates\Model\Converter\Product;

use MageWorx\SeoXTemplates\Model\Converter\Product as ConverterProduct;
use Magento\Framework\Pricing\Helper\Data as HelperPrice;
use MageWorx\SeoXTemplates\Helper\Data as HelperData;
use Magento\Tax\Helper\Data as HelperTax;
use Magento\Catalog\Model\Product\Url as ProductUrl;
use MageWorx\SeoXTemplates\Helper\Converter as HelperConverter;

class Url extends ConverterProduct
{
    /**
     *
     * @var Url
     */
    protected $url;

    /**
     *
     * @param ProductUrl $url
     * @param HelperData $helperData
     */

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        HelperData $helperData,
        HelperConverter $helperConverter,
        \MageWorx\SeoXTemplates\Model\ResourceModel\Category $resourceCategory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        HelperPrice $helperPrice,
        HelperTax $helperTax,
        ProductUrl $url
    ) {
        $this->url = $url;
        parent::__construct($storeManager, $helperData, $helperConverter, $resourceCategory, $request, $resourceProduct, $helperPrice, $helperTax);
    }

    /**
     *
     * @return string
     */
    protected function _convertStoreViewName()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    protected function _convertStoreName()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    protected function _convertWebsiteName()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    protected function _convertCategory()
    {
        return '';
    }

    /**
     *
     * @return string
     */
    protected function _convertCategories()
    {
        return '';
    }

    /**
     *
     * @param string $convertValue
     * @return string
     */
    protected function _render($convertValue)
    {
        $convertValue = parent::_render($convertValue);
        return $this->url->formatUrlKey($convertValue);
    }
}
