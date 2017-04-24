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
 * @package   mirasvit/module-report
 * @version   1.2.1
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Ui;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Model\Collection;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @var Context
     */
    protected $uiContext;

    /**
     * @var MapRepositoryInterface
     */
    protected $mapRepository;

    public function __construct(
        Context $uiContext,
        MapRepositoryInterface $mapRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ContextInterface $context,
        array $meta = [],
        array $data = []
    ) {
        $this->context = $context;
        $this->mapRepository = $mapRepository;
        $this->uiContext = $uiContext;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->reset();
    }

    /**
     * @return void
     */
    public function reset()
    {
        $this->collection = $this->uiContext->getReport()->getCollection();
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getData()
    {
        $startTime = microtime(true);

        $searchResult = $this->getSearchResult()->toArray();

        foreach ($searchResult['items'] as $key => $item) {
            foreach ($item as $code => $value) {
                if ($code != '1') {
                    $column = $this->mapRepository->getColumn($code);

                    if ($column) {
                        $searchResult['items'][$key][$code . '_orig'] = $searchResult['items'][$key][$code];
                        $searchResult['items'][$key][$code] = $column->prepareValue($value);
                        $searchResult['items'][$key][$code] = $this->uiContext->getReport()->prepareValue(
                            $column,
                            $searchResult['items'][$key][$code],
                            $searchResult['items'][$key]
                        );
                    }
                }
            }
        }

        $totals = $this->collection->getTotals();

        if (is_array($totals)) {
            foreach ($totals as $code => $value) {
                if ($code != '1') {
                    $column = $this->mapRepository->getColumn($code);

                    if (in_array($column->getDataType(), ['number', 'price'])) {
                        if ($column) {
                            $totals[$code . '_orig'] = $totals[$code];
                            $totals[$code] = $column->prepareValue($value);
                            $totals[$code] = $this->uiContext->getReport()
                                ->prepareValue($column, $totals[$code], $totals);
                        }
                    } else {
                        $totals[$code] = '';
                    }
                }
            }
        }

        $result = [
            'totalRecords'    => $this->getCollection()->getSize(),
            'items'           => array_values($searchResult['items']),
            'totals'          => [$totals->toArray()],
            'dimensionColumn' => $this->uiContext->getActiveDimension(),
            'select'          => $this->getCollection()->__toString(),
            'time'            => round(microtime(true) - $startTime, 4),
        ];

        return $result;
    }


    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $this->uiContext->getReport()->getCollection()
            ->addColumnToSelect($this->mapRepository->getColumn($this->uiContext->getActiveDimension()));

        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function addField($field, $alias = null)
    {
        $column = $this->mapRepository->getColumn($field);

        $this->collection->addColumnToSelect($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction)
    {
        $column = $this->mapRepository->getColumn($field);

        $this->collection->addColumnToOrder(
            $column,
            $direction
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $column = $this->mapRepository->getColumn($filter->getField());

        $this->collection->addColumnToFilter(
            $column,
            [$filter->getConditionType() => $filter->getValue()]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDimension($column)
    {
        if (!is_object($column)) {
            $column = $this->mapRepository->getColumn($column);
        }

        $this->collection->setColumnToGroup($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($page, $size)
    {
        $this->collection
            ->setPageSize($size)
            ->setCurPage($page);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $this->data['config']['params'] = [
            'report' => $this->uiContext->getReport()->getIdentifier(),
        ];

        return isset($this->data['config']) ? $this->data['config'] : [];
    }
}
