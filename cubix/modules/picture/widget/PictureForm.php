<?php
/**
 * Openbiz Cubi Application Platform
 *
 * LICENSE http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 *
 * @package   cubi.picture.widget
 * @copyright Copyright (c) 2005-2011, Openbiz Technology LLC
 * @license   http://code.google.com/p/openbiz-cubi/wiki/CubiLicense
 * @link      http://code.google.com/p/openbiz-cubi/
 * @version   $Id: PictureForm.php 4224 2012-09-16 14:16:02Z rockyswen@gmail.com $
 */

class PictureForm extends PickerForm
{
	public $basePath = 'picture';
	
	// keep canUpdate in session
	public function loadSessionVars($sessionContext)
    {
        parent::loadSessionVars($sessionContext);
		$sessionContext->loadObjVar($this->objectName, "CanUpdateRecord", $this->canUpdateRecord);
	}
	
	public function saveSessionVars($sessionContext)
    {
        parent::saveSessionVars($sessionContext);
		$sessionContext->saveObjVar($this->objectName, "CanUpdateRecord", $this->canUpdateRecord);
	}
	
	public function uploadFile()
	{
		if (empty($_FILES)) return;
		
		$upload_user_dir = BizSystem::getUserProfile("Id");						
		$upload_user_dir = (int)$upload_user_dir;
		$upload_dir = "common";
		
		try {
            $parentForm = BizSystem::getObject($this->parentFormName);		
            $cond_value = $parentForm->getDataObj()->association['CondValue'];
            if($cond_value)
            {
                $upload_dir = $cond_value;
            }
                                
            if(!file_exists(OPENBIZ_PUBLIC_UPLOAD_PATH.DIRECTORY_SEPARATOR.$this->basePath.DIRECTORY_SEPARATOR.$upload_dir.DIRECTORY_SEPARATOR.$upload_user_dir)) {
                @mkdir(OPENBIZ_PUBLIC_UPLOAD_PATH.DIRECTORY_SEPARATOR.$this->basePath.DIRECTORY_SEPARATOR.$upload_dir.DIRECTORY_SEPARATOR.$upload_user_dir,0777,true);
            }				
            
            $targetPath = OPENBIZ_PUBLIC_UPLOAD_PATH.DIRECTORY_SEPARATOR.$this->basePath.DIRECTORY_SEPARATOR.$upload_dir.DIRECTORY_SEPARATOR.$upload_user_dir.DIRECTORY_SEPARATOR;
            
            $targetURL = OPENBIZ_PUBLIC_UPLOAD_URL."/".$this->basePath."/".$upload_dir."/".$upload_user_dir."/";
            
            $tempFile = $_FILES['Filedata']['tmp_name'];	
            $newFilename = 	date("YmdHis")."_".uniqid().'.jpg';
            $targetFile =  str_replace('//',DIRECTORY_SEPARATOR,$targetPath) . $newFilename;
            
            move_uploaded_file($tempFile,$targetFile);
            
		} catch(Exception $e){
			file_put_contents(OPENBIZ_APP_PATH.'\out.txt', $e->getMessage());			
		}
		$output =array();
		$output['file_path'] = $targetPath.$newFilename;
		$output['file_url'] = $targetURL.$newFilename;
		$output['file_name'] = $_FILES['Filedata']['name'];

		echo base64_encode(json_encode($output));
        exit;
	}
	
	public function checkFile()
	{
		$fileArray = array();
		foreach ($_POST as $key => $value) {
			if ($key != 'folder') {
				if (file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['folder'] . '/' . $value)) {
					$fileArray[$key] = $value;
				}
			}
		}
		echo json_encode($fileArray);	
		exit;
	}
	
	public function fileUploadComplete($fileObjStr){	
		
		$fileObj = json_decode(base64_decode($fileObjStr),true);
		
		$recArr = $this->readInputRecord();
	    $this->setActiveRecord($recArr);
        if (count($recArr) == 0)
            return;

        try
        {
            $this->ValidateForm();
        }
        catch (ValidationException $e)
        {
            $this->processFormObjError($e->errors);
            return;
        }
        
        //add file attributes
        $recArr['filesize'] =  filesize($fileObj['file_path']);
        $recArr['md5'] = md5_file($fileObj['file_path']);
        $recArr['sha256'] = sha1_file($fileObj['file_path']);
        $recArr['filename'] = $fileObj['file_name'];
        $recArr['path'] = $fileObj['file_path'];
        $recArr['url'] = $fileObj['file_url'];
        $recArr['download_count'] = 0;
        
		if (!$this->parentFormElemName)
        {
        	//its only supports 1-m assoc now	        	        
	        $parentForm = BizSystem::objectFactory()->getObject($this->parentFormName);
        	//$parentForm->getDataObj()->clearSearchRule();
	        $parentDo = $parentForm->getDataObj();
	        
	        $column = $parentDo->association['Column'];
	    	$field = $parentDo->getFieldNameByColumn($column);	    	    	
	    	$parentRefVal = $parentDo->association["FieldRefVal"];
	    	
			$recArr[$field] = $parentRefVal;
	    	$cond_column = $parentDo->association['CondColumn'];
	    	$cond_value = $parentDo->association['CondValue'];
	    	if($cond_column)
	    	{
	    		$cond_field = $parentDo->getFieldNameByColumn($cond_column);
	    		$recArr[$cond_field] = $cond_value;
	    	}    	
        }                

        if ($this->parentFormElemName && $this->pickerMap)
        {
            return ; //not supported yet
        }
        $recId = $parentDo->InsertRecord($recArr);
            
        $selIds[] = $recId;

		exit;
	}
	
	public function allUploadComplete(){
		$this->close();	
		$parentForm = BizSystem::getObject($this->parentFormName);
		$parentForm->rerender();
	}
	
    public function loadDialog($formName, $id=null)
    {
    	$paramFields = array();
        if ($id==null && $this->recordId!=null)
        {
        	$id = $this->recordId;
        }
        if($id!=null)
            $paramFields["Id"] = $id;
        $this->_showForm($formName, "Dialog", $paramFields);
    }	
	
	public function DeleteRecord($id=null){		
        if ($id==null || $id=='')
            $id = BizSystem::clientProxy()->getFormInputs('_selectedId');

        $selIds = BizSystem::clientProxy()->getFormInputs('row_selections', false);
        if ($selIds == null)
            $selIds[] = $id;
        foreach ($selIds as $id)
        {        	
            $dataRec = $this->getDataObj()->fetchById($id);
            if(!$dataRec){
            	continue;
            }
            //remove file 
            $file = $dataRec['path'];
            @unlink($file);
            
            if(!$this->canDeleteRecord($dataRec))
            {
            	$this->errorMessage = $this->getMessage("FORM_OPEATION_NOT_PERMITTED",$this->objectName);         
        		if (strtoupper($this->formType) == "LIST"){
        			BizSystem::log(LOG_ERR, "DATAOBJ", "DataObj error = ".$errorMsg);
        			BizSystem::clientProxy()->showClientAlert($this->errorMessage);
        		}else{
        			$this->processFormObjError(array($this->errorMessage));	
        		}	
        		return;
            }
            
            // take care of exception
            try
            {
                $dataRec->delete();
            } catch (BDOException $e)
            {
                // call $this->processBDOException($e);
                $this->processBDOException($e);
                return;
            }
        }
        if (strtoupper($this->formType) == "LIST")
            $this->rerender();

        $this->runEventLog();
        $this->processPostAction();		
	}
	
	public function close(){
		$parentForm = BizSystem::getObject($this->parentFormName);
		$parentForm->rerender();
		return parent::close();
	}
	
	

}
?>