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


namespace Mirasvit\Report\Model\Query\Eav;

use Magento\Framework\App\ResourceConnection;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Model\Query\FieldFactory;
use Mirasvit\Report\Model\Query\Eav\FieldFactory as EavFieldFactory;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Mirasvit\Report\Model\Query\ColumnFactory;
use Magento\Eav\Model\Config;


class Table extends \Mirasvit\Report\Model\Query\Table
{
    /**
     * @var EavEntityFactory
     */
    protected $eavEntityFactory;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ResourceConnection $resource,
        MapRepositoryInterface $mapRepository,
        EavEntityFactory $eavEntityFactory,
        Config $eavConfig,
        $name,
        $type,
        $connection = 'default'
    ) {
        parent::__construct($resource, $mapRepository, $name, $connection);

        $this->eavEntityFactory = $eavEntityFactory;
        $this->eavConfig = $eavConfig;

        $this->initByEntityType($type);
    }

    /**
     * @param string $type
     * @return void
     */
    protected function initByEntityType($type)
    {
        $entityTypeId = (int)$this->eavEntityFactory->create()->setType($type)->getTypeId();

        $attributeCodes = $this->eavConfig->getEntityAttributeCodes($entityTypeId);
        foreach ($attributeCodes as $attributeCode) {
            $attribute = $this->eavConfig->getAttribute($entityTypeId, $attributeCode);

            $field = $this->mapRepository->createEavField([
                'table'        => $this,
                'name'         => $attributeCode,
                'entityTypeId' => $type,
            ]);

            $this->fieldsPool[$field->getName()] = $field;

            if ($attribute->getDefaultFrontendLabel()) {
                $options = false;

                if ($attribute->usesSource()) {
                    $options = $attribute->getSource()->toOptionArray();
                }

                $this->mapRepository->createColumn([
                    'name' => $attributeCode,
                    'data' => [
                        'label'   => $attribute->getDefaultFrontendLabel(),
                        'type'    => $attribute->getFrontendInput(),
                        'options' => $options,
                        'table'   => $this,
                        'fields'  => [
                            $attributeCode,
                        ],
                    ],
                ]);
            }
        }
    }
}
