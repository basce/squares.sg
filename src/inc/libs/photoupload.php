<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'msg':'increase post_max_size $postSize and upload_max_filesize $uploadSize  to ".$this->sizeLimit." .current : ".ini_get('post_max_size')." & ".ini_get('upload_max_filesize')."'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
	
	private function getMetaInfo($imagePath){
		$exif_ifd0 = read_exif_data($imagePath ,'IFD0' ,0);       
   		$exif_exif = read_exif_data($imagePath ,'EXIF' ,0);
		
		$interestTags = array("Make", "Model", "ExposureTime", "FNumber", "ISOSpeedRatings","ExposureBiasValue","MeteringMode","FocalLength");
		$filteredMeta = array();
		foreach($interestTags as $tag){
			if(array_key_exists($tag, $exif_ifd0)){
				$filteredMeta[$tag] = $exif_ifd0[$tag];
			}else if(array_key_exists($tag, $exif_exif)){
				$filteredMeta[$tag] = $exif_exif[$tag];
			}
		}
		return $filteredMeta;
	}
    
    /**
     * Returns array('success'=>true) or array('msg'=>'error message')
     */
    function handleUpload($uploadDirectory, $newfilename, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error'=>1, 'msg' => "*Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error'=>1, 'msg' => '*No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error'=>1, 'msg' => '*File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error'=>1, 'msg' => '*Please submit an image file that is not more than 12MB');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $newfilename;
        //$filename = md5(uniqid());
        $ext = strtolower($pathinfo['extension']);

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error'=>1, 'msg' => '*File has an invalid extension. Only '. $these . ' files are accepted');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
			$metaInfo = $this->getMetaInfo($uploadDirectory . $filename . '.' . $ext);
            return array('error'=>0, "type"=>$ext, "file"=>$filename, 'extension'=>$ext, 'meta'=>$metaInfo, 'path'=>$uploadDirectory . $filename . '.' . $ext);
        } else {
            return array('error'=>1, 'msg'=> '*Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
}
?>