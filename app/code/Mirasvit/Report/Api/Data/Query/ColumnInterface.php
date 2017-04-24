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


namespace Mirasvit\Report\Api\Data\Query;

interface ColumnInterface
{
    const TYPE_EXPRESSION  = 'expression';
    const TYPE_AGGREGATION = 'aggregation';
    const TYPE_SIMPLE      = 'simple';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getDataType();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     * @deprecated
     */
    public function getColor();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $value
     * @return string
     * @deprecated
     */
    public function prepareValue($value);

    /**
     * @return \Zend_Db_Expr
     */
    public function toDbExpr();

    /**
     * @return FieldInterface[]
     */
    public function getFields();

    /**
     * @return array
     * @deprecated
     */
    public function getJsConfig();
}