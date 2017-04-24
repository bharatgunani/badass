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


namespace Mirasvit\Report\Model\Export;

use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Exception\LocalizedException;

class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     * @param Filesystem       $filesystem
     * @param Filter           $filter
     * @param MetadataProvider $metadataProvider
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider
    ) {
        parent::__construct($filesystem, $filter, $metadataProvider);
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();

        $fields = $this->metadataProvider->getFields($component);

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $data = $dataProvider->getData();
        if (count($data['items']) > 0) {
            foreach ($data['items'] as $item) {
                $stream->writeCsv($this->prepareData($item, $fields));
            }
        }
        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true  // can delete file after use
        ];
    }

    /**
     * @param array $item
     * @param array $fields
     * @return array
     */
    protected function prepareData($item, $fields = [])
    {
        if (!$fields) {
            return $item;
        }
        $data = [];
        foreach ($fields as $field) {
            if (isset($item[$field])) {
                $data[] = $item[$field];
            }
        }

        return $data;
    }
}
