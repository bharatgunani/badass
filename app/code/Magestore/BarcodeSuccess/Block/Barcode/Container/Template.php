<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Block\Barcode\Container;

use Magestore\BarcodeSuccess\Model\Source\MeasurementUnit;
use Magestore\BarcodeSuccess\Model\Source\TemplateType;

class Template extends \Magestore\BarcodeSuccess\Block\Barcode\Container
{

    public function getBarcodes(){
        $barcodes = [];
        $datas = $this->getData('barcodes');
        if($datas){
            $template = $this->getTemplateData();
            foreach ($datas as $data){
                if(empty($data['qty'])){
                    $data['qty'] = $template['label_per_row'];
                }
                for($i = 1; $i <= $data['qty']; $i++){
                    $barcodes[] = $this->getBarcodeSource($data);
                }
            }
        }
        return $barcodes;
    }

    /**
     * @return string
     */
    public function getBarcodeSource($data){
        $source = "";
        if($data){
            $template = $this->getTemplateData();
            $type = $template['symbology'];
            $barcodeOptions = array('text' => $data['barcode'],
                'fontSize' => $template['font_size']
            );
            $rendererOptions = array(
                //'width' => '198',
                'height' => '0',
                'imageType' => 'png'
            );
            $source = \Zend_Barcode::factory(
                $type, 'image', $barcodeOptions, $rendererOptions
            );
        }
        return $source;
    }

    /**
     * @return string
     */
    public function getTemplateData(){
        $data = [];
        if($this->getData('template_data')){
            $data = $this->getData('template_data');
        }
        if(empty($data['font_size'])){
            $data['font_size'] = '24';
        }
        if(empty($data['label_per_row'])){
            $data['label_per_row'] = '1';
        }
        if(empty($data['measurement_unit'])){
            $data['measurement_unit'] = MeasurementUnit::MM;
        }
        if(empty($data['paper_height'])){
            $data['paper_height'] = '30';
        }
        if(empty($data['paper_width'])){
            $data['paper_width'] = '100';
        }
        if(empty($data['label_height'])){
            $data['label_height'] = '30';
        }
        if(empty($data['label_width'])){
            $data['label_width'] = '100';
        }
        if(empty($data['left_margin'])){
            $data['left_margin'] = '0';
        }
        if(empty($data['right_margin'])){
            $data['right_margin'] = '0';
        }
        if(empty($data['bottom_margin'])){
            $data['bottom_margin'] = '0';
        }
        if(empty($data['top_margin'])){
            $data['top_margin'] = '0';
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function isJewelry(){
        $template = $this->getTemplateData();
        return ($template['type'] == TemplateType::JEWELRY)?true:false;
    }
}
