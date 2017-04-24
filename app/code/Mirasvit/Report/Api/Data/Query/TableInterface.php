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

interface TableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return ColumnInterface[]
     */
    public function getColumns();

    /**
     * @param string $name
     * @return ColumnInterface
     */
    public function getColumn($name);

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function addColumn(ColumnInterface $column);

    /**
     * @param string $name
     * @return FieldInterface
     */
    public function getField($name);

    /**
     * @return string
     */
    public function getConnectionName();
}
