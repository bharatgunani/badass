<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoUrls\Plugin\LayerFilterItem;

use \MageWorx\SeoUrls\Model\Source\PagerMask;
use Magento\Framework\View\Element\Template;
use MageWorx\SeoAll\Helper\Layer as SeoAllHelperLayer;

class AfterGetUrl
{
    /**
     * @var \MageWorx\SeoUrls\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoUrls\Helper\Url
     */
    protected $helperUrl;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \MageWorx\SeoUrls\Helper\Layer
     */
    protected $helperLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \MageWorx\SeoUrls\Helper\UrlBuilder\Layer
     */
    protected $seoLayerUrlBuilder;

    /**
     * @var SeoAllHelperLayer
     */
    protected $helperLayerAll;

    /**
     * AfterGetUrl constructor.
     * @param \MageWorx\SeoUrls\Helper\Data $helperData
     * @param \MageWorx\SeoUrls\Helper\Url $helperUrl
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \MageWorx\SeoUrls\Helper\Layer $helperLayer
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \MageWorx\SeoUrls\Helper\UrlBuilder\Layer $seoLayerUrlBuilder
     */
    public function __construct(
        \MageWorx\SeoUrls\Helper\Data $helperData,
        \MageWorx\SeoUrls\Helper\Url $helperUrl,
        \Magento\Framework\App\RequestInterface $request,
        \MageWorx\SeoUrls\Helper\Layer $helperLayer,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \MageWorx\SeoUrls\Helper\UrlBuilder\Layer $seoLayerUrlBuilder,
        SeoAllHelperLayer $helperLayerAll
    ) {
        $this->request            = $request;
        $this->helperData         = $helperData;
        $this->helperUrl          = $helperUrl;
        $this->helperLayer        = $helperLayer;
        $this->categoryHelper     = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->seoLayerUrlBuilder = $seoLayerUrlBuilder;
        $this->helperLayerAll     = $helperLayerAll;
    }

    public function afterGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $filterItem, $url)
    {
        if ($this->out()) {
            return $url;
        }

        if ($filterItem->getFilter() instanceof \Magento\CatalogSearch\Model\Layer\Filter\Category
            || $filterItem->getFilter() instanceof \Magento\Catalog\Model\Layer\Filter\Category
        ) {
            $url = $this->getCategoryFilterUrl($filterItem);
        } else {
            $url = $this->getAttributeFilterUrl($filterItem);
        }

        return $url;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return string
     */
    public function getAttributeFilterUrl($filterItem)
    {
        $varName = $filterItem->getFilter()->getRequestVar();
        $value   = $this->getAttributeValue($filterItem);

        $query = [
            $varName => $value,
            $this->helperData->getPagerVariableName() => null // exclude current page from urls
        ];

        $url = $this->seoLayerUrlBuilder->getLayerFilterUrl(
            [
                '_current'     => true,
                '_use_rewrite' => true,
                '_query'       => $query
            ]
        );

        return $url;
    }

    /**
     * Retrieve attribute value (depends by attribute type)
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return mixed
     */
    public function getAttributeValue($filterItem)
    {
        $labelValues = [];

        if (method_exists($filterItem->getFilter(), 'getAttributeValues')) {
            $values = $filterItem->getFilter()->getAttributeValues();

            if ($values) {
                foreach ($values as $optionId) {
                    $labelValues[] = $filterItem->getFilter()->getAttributeModel()->getFrontend()->getOption($optionId);
                }
            }
        }

        $labelValues[] = $filterItem->getLabel();

        $attribute  = $filterItem->getFilter()->getData('attribute_model'); //->getAttributeCode()
        if ($attribute) {
            if ($attribute->getAttributeCode() == 'price' || $attribute->getBackendType() == 'decimal') {
                return $filterItem->getValue();
            }
        }

        return implode($this->helperLayerAll->getMultipleValueSeparator(), $labelValues);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryFilterUrl($filterItem)
    {
        $category    = $this->categoryRepository->get((int)$filterItem->getValue());
        $categoryUrl =  $this->categoryHelper->getCategoryUrl($category);
        $suffix      = $this->helperData->getCategorySuffix();

        if ($suffix == "/") {
            $suffix = '';
        }
        if ($suffix && strpos($suffix, '.') === false) {
            $suffix = '.' . $suffix;
        }

        $categoryPart = $this->helperUrl->removeSuffix($categoryUrl, $suffix);
        $layeredPart  = $this->getLayeredPartFromUrl($filterItem);

        $categoryPart = str_replace('?___SID=U', '', $categoryPart);

        $url = $categoryPart . $layeredPart . $suffix;

        return $url;
    }

    /**
     * @return bool
     */
    protected function getIsCategoryAnchor()
    {
        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $filterItem
     * @return string
     */
    public function getLayeredPartFromUrl($filterItem)
    {
        /*
        if($this->getIsCategoryAnchor($filterItem->getValue())){
            $layeredNavIdentifier = $this->helperData->getSeoUrlIdentifier();

            if (preg_match("/\/$layeredNavIdentifier\/.+/", $this->request->getOriginalPathInfo(), $matches)) {
                $layeredpart = ($suffix && substr($matches[0], -(strlen($suffix))) == $suffix ? substr($matches[0], 0,
                    -(strlen($suffix))) : $matches[0]);
            }
            else {
                $layeredpart = '';
            }
        }else{
            $layeredpart = '';
        }
        */
        return '';
    }

    /**
     * @return bool
     */
    protected function out()
    {
        if (!$this->helperData->getIsSeoFiltersEnable()) {
            return true;
        }

        if ($this->request->getFullActionName() !== 'catalog_category_view') {
            return true;
        }

        return false;
    }
}
