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


namespace Mirasvit\Report\Model\Query;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Api\Data\Query\FieldInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;

class Column implements ColumnInterface
{
    /**
     * @var int
     */
    protected static $colorIndex = 0;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var TableInterface
     */
    protected $table;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var string|array
     */
    protected $options;

    /**
     * @var string
     */
    protected $selectType;

    /**
     * @var string
     */
    protected $color;

    /**
     * @var FieldInterface[]
     */
    protected $fieldsPool = [];

    /**
     * @param MapRepositoryInterface $mapRepository
     * @param string                 $name
     * @param array                  $data
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct(
        MapRepositoryInterface $mapRepository,
        $name,
        $data = []
    ) {
        $this->name = $name;

        $this->selectType = self::TYPE_SIMPLE;

        $this->expression = isset($data['expr']) ? $data['expr'] : '%1';
        $this->label = isset($data['label']) ? $data['label'] : '';
        $this->dataType = isset($data['type']) ? $data['type'] : '';
        $this->options = isset($data['options']) ? $data['options'] : [];
        $this->color = isset($data['color']) ? $data['color'] : '';

        $this->table = $data['table'];
        $this->table->addColumn($this);

        if (isset($data['fields'])) {
            foreach ($data['fields'] as $field) {
                $field = explode('.', $field);
                if (count($field) == 1) {
                    $this->fieldsPool[] = $this->table->getField($field[0]);
                } else {
                    $table = $mapRepository->getTable($field[0]);
                    $this->fieldsPool[] = $table->getField($field[1]);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->table->getName() . '|' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->selectType;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return void
     */
    protected function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param array $fields
     * @return $this
     */
    protected function setFields($fields)
    {
        $this->fieldsPool = [];
        foreach ($fields as $field) {
            $this->fieldsPool[] = $this->table->getField($field);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColor()
    {
        if ($this->color == null) {
            $colors = [
                '#FFD963', #yellow
                '#FF5A3E', #red
                '#77B6E7', #blue
                '#97CC64', #green
                '#A9B9B8',
                '#DC9D6B',
            ];

            $this->color = $colors[self::$colorIndex];
            if (++self::$colorIndex >= count($colors)) {
                self::$colorIndex = 0;
            }
        }

        return $this->color;
    }

    /**
     * @param string $expression
     * @return $this
     */
    protected function setExpression($expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fieldsPool;
    }

    /**
     * {@inheritdoc}
     */
    public function toDbExpr()
    {
        $exr = $this->expression;
        $idx = 1;

        foreach ($this->fieldsPool as $field) {
            $exr = str_replace('%' . $idx, $field->toDbExpr(), $exr);
            $idx++;
        }

        return new \Zend_Db_Expr($exr);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValue($value)
    {
        return $value;
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        return [
            'column' => $this->getName(),
            'label'  => $this->getLabel(),
        ];
    }
}
