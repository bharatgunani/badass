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


namespace Mirasvit\Report\Model\Query\Column;

use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Model\Query\Column;

class Sum extends Column
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        MapRepositoryInterface $mapRepository,
        $name,
        $data = []
    ) {
        $name = 'sum_' . $name;

        parent::__construct($mapRepository, $name, $data);

        $this->selectType = self::TYPE_AGGREGATION;

        $this->expression = 'IFNULL(SUM(' . $this->expression . '), 0)';
    }
}
