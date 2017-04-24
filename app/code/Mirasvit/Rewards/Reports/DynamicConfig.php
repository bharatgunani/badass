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


namespace Mirasvit\Rewards\Reports;

use Mirasvit\Report\Model\Config\Map;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\App\Cache\TypeListInterface;

class DynamicConfig
{
    /**
     * @var array
     */
    protected $cacheTypes = ['config'];
    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var string
     */
    private $sampleFileName = 'mreport.xml.sample';

    /**
     * @var string
     */
    private $fileName = 'mreport.xml';

    public function __construct(Map $map, TypeListInterface $cacheTypeList, Reader $reader)
    {
        $this->map           = $map;
        $this->reader        = $reader;
        $this->cacheTypeList = $cacheTypeList;

        $this->prepareConfigFile();
    }

    /**
     * @return void
     */
    private function prepareConfigFile()
    {
        $confPath = $this->reader->getModuleDir('etc', 'Mirasvit_Rewards');
        if (!file_exists($confPath.DIRECTORY_SEPARATOR.$this->fileName)) {
            copy($confPath.DIRECTORY_SEPARATOR.$this->sampleFileName, $confPath.DIRECTORY_SEPARATOR.$this->fileName);
        }
    }

    /**
     * @param array $tables
     * @param array $fields
     *
     * @return bool
     */
    public function updateConfig($tables, $fields)
    {
        $confPath = $this->reader->getModuleDir('etc', 'Mirasvit_Rewards');
        $xml      = $confPath.DIRECTORY_SEPARATOR.$this->fileName;
        $dom      = new \DOMDocument();

        $useErrors = libxml_use_internal_errors(true);

        if (!$dom->load($xml)) {
            libxml_use_internal_errors($useErrors);
            return false;
        }

        $modified = false;
        foreach ($tables as $table) {
            $query = '//config/table[@name = "' . $table . '"]/columns';

            $xpath   = new \DOMXPath($dom);
            $columns = $xpath->query($query);
            foreach ($columns as $column) {
                foreach ($fields as $field) {
                    $xpath = new \DOMXPath($dom);
                    $fieldsColumns = $xpath->query($query . '/column[@name = "' . $field . '"]');
                    if (!$fieldsColumns->length) {
                        $node = $this->createColumn($dom, $field);
                        $column->appendChild($node);
                        $modified = true;
                    }
                }
            }
        }

        if ($modified) {
            $dom->save($xml);

            libxml_use_internal_errors($useErrors);

            $this->clearCache();
        }
    }

    /**
     * @param \DOMDocument $dom
     * @param string       $field
     * @return \DOMElement
     */
    protected function createColumn($dom, $field)
    {
        if (empty($this->nodes[$field])) {
            $node = $dom->createElement("column");
            $node->setAttribute('name', $field);
            $node->setAttribute('label', ucwords(str_replace('_', ' ', $field)));
            $node->setAttribute('fields', $field);
            $node->setAttribute('type', 'number');
            $node->setAttribute('expr', 'ABS(SUM(%1))');

            $this->nodes[$field] = $node;
        }

        return $this->nodes[$field];
    }

    /**
     * @return void
     */
    protected function clearCache()
    {
        foreach ($this->cacheTypes as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }
}