<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Controller\Adminhtml\Template;

/**
 * Class Save
 * @package Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
 */
class Save extends \Magestore\BarcodeSuccess\Controller\Adminhtml\AbstractTemplate
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $path = '*/*/';
        $resultRedirect = $this->resultRedirectFactory->create();
        try{
            $params = $this->getRequest()->getParams();
            if(isset($params['template_id']) && empty($params['template_id'])){
                unset($params['template_id']);
            }
            $model = $this->helper->getModel('Magestore\BarcodeSuccess\Model\Template');
            $model->addData($params);
            $this->resource->save($model);
            $this->messageManager->addSuccessMessage(__('The template has been saved successfully'));
        }catch (\Exception $ex){
            $this->messageManager->addErrorMessage($ex->getMessage());
        }
        return $resultRedirect->setPath($path);
    }
}
