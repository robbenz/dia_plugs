<?php
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

class ioaphp {
    /**
     * Authentication values array
     * @var array
     */
    protected $auth = array();
    
    /**
     * Image Optimize API Url
     * @var string api url
     */
    protected $apiUrl = 'https://api.imagerecycle.com/v1/';

    /**
     * Last Error message
     * @var string 
     */
    protected $lastError = null;
    
     /**
     * Last Error code
     * @var string 
     */
    protected $lastErrCode = null;

    /**
     * 
     * @param string $key
     * @param string $secret
     */
    public function __construct($key,$secret){
	$this->auth = array('key'=>$key, 'secret'=>$secret);	
    }
    
    /**
     * Change the API URL
     * @param string $url
     */
    public function setAPIUrl($url){
	$this->apiUrl = $url;
    }
    
    /**
     * Upload a file sent through an html post form
     * @param $_FILES $file posted file
     */
    public function uploadFile($file,$params=array()){
	if(class_exists('CURLFile')){
	    $curlFile = new CURLFile($file);
	}else if( function_exists('curl_version')) { 
            $curlFile = '@'.$file;
        }
        else{
	    $curlFile = $file;
	}	
	$params = array(
	    'auth' => json_encode($this->auth),
	    'file' => $curlFile,            
	    'params' => json_encode($params)
	);
	try {                      
	    $result = $this->callAPI($this->apiUrl.'images/','POST',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
            $this->lastErrCode = $exc->getCode();
	    return false;
	}
	return $result;
    }
    
    /**
     * Upload a file from an url
     * @param string $url
     * @return Object
     */
    public function uploadUrl($url,$params=array()){		
	$params = array(
	    'auth' => json_encode($this->auth),
            'url' => $url,
	    'params' => json_encode($params)	  
	);
	try {
	    $result = $this->callAPI($this->apiUrl.'images/','POST',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Call the API with curl
     * @param string $url
     * @param string $type HTTP method
     * @param array $datas 
     * @return type
     */
    protected function callAPI($url,$type,$datas){
         
        if( function_exists('curl_version')) {  
            $curl = curl_init();	
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl, CURLOPT_TIMEOUT, 300);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
             //fix windows localhost ssl problem
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            if($type==='POST'){
                curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
            }else{
                $url .= '?'.http_build_query($datas);
            }
            curl_setopt($curl, CURLOPT_URL, $url);
            $content = curl_exec($curl);
            $infos = curl_getinfo($curl);
            $infos['http_code'] = (String)$infos['http_code'];
            if($infos['http_code'][0]!=='2'){
                $error = json_decode($content);
                if(isset($error->errCode)){
                    $errCode = $error->errCode;
                }else{
                    $errCode = 0;
                }
                if(isset($error->errMessage)){
                    $errMessage = $error->errMessage;
                }else{
                    $errMessage = curl_error($curl);//'An error occurs';// 
                }
                throw new Exception($errMessage,$errCode);
            }
            curl_close($curl);
        }else {
            $content = $this->rest_helper($url,$datas,$type);            
        }
        //var_dump($content);
	return json_decode($content);
    }
      
    function rest_helper($url, $params = null, $type = 'GET')
    {
      $cparams = array(
        'http' => array(
          'header' => "Content-Type: application/x-www-form-urlencoded\r\n".                
                     "User-Agent:MyAgent/1.0\r\n",
          'method' => $type,
          'ignore_errors' => true
        )
      );
      if ($params !== null) {
        if(isset($params['file'])) {
            $upload_file = $params['file'];
            unset($params['file']);
        }else {
            $upload_file = "";
        }
        
        if ($type == 'POST') {
             $data = ""; 
                $boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10); 

            //Collect Postdata 
            foreach($params as $key => $val) 
            { 
                $data .= "--$boundary\n"; 
                $data .= "Content-Disposition: form-data; name=\"".$key."\"\n\n".$val."\n"; 
            } 

            $data .= "--$boundary\n"; 
            if($upload_file) {
                $fileContents = file_get_contents($upload_file); 
                $fileName = basename($upload_file);
                $data .= "Content-Disposition: form-data; name=\"file\"; filename=\"{$fileName}\"\n"; 
                $data .= "Content-Type: image/jpeg\n"; 
                $data .= "Content-Transfer-Encoding: binary\n\n"; 
                $data .= $fileContents."\n"; 
                $data .= "--$boundary--\n"; 
            }
           
            $cparams['http']['header'] = 'Content-Type: multipart/form-data; boundary='.$boundary;
            $cparams['http']['content'] = $data; 
        } else {
            $params = http_build_query($params);
            $url .= '?' . $params; 
        }
      }

      $context = stream_context_create($cparams);
      $fp = fopen($url, 'rb', false, $context);
      if (!$fp) {
        $res = false;
      } else {
        // If you're trying to troubleshoot problems, try uncommenting the
        // next two lines; it will show you the HTTP response headers across
        // all the redirects:
        // $meta = stream_get_meta_data($fp);
        // var_dump($meta['wrapper_data']);
        $res = stream_get_contents($fp);
      }

      if ($res === false) {
        throw new Exception("$type $url failed: $php_errormsg");
      }
    
      return $res;
    }
    /**
     * Get all the images
     * @return type
     */
    public function getImagesList($offset=0, $limit=30){
	$params = array(	  
	    'auth' => json_encode($this->auth),
	    'params' => '',
	    'offset' => $offset,
	    'limit' => $limit
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'images/','GET',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Get one image
     * @param int $id
     * @return type
     */
    public function getImage($id){
	$params = array(
	    'auth' => json_encode($this->auth),
	    'params' => ''
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'images/'.(int)$id,'GET',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Delete an image 
     * @param int $id
     * @return type
     */
    public function deleteImage($id){
	$params = array(	
	    'auth' => json_encode($this->auth),
	    'params' => ''
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'images/'.(int)$id,'DELETE',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Get account information
     * @return type
     */
    public function getAccountInfos(){
	$params = array(
	    'auth' => json_encode($this->auth),
	    'params' => ''
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'accounts/mine','GET',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Get last error message
     * @return string
     */
    public function getLastError(){
	return $this->lastError;
    }
    public function getLastErrCode(){
	return $this->lastErrCode;
    }
}
?>