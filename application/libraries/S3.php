<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *	CodeIgniter Amazon S3 library in PHP by zairwolf
 *
 *	Source: https://github.com/zairwolf/CodeIgniter-AmazonS3/blob/master/S3.php
 *
 *	Author: Hai Zheng @ https://www.linkedin.com/in/zairwolf/
 *
 */
require_once '../vendor/autoload.php';
require_once 'REST_Controller.php';
use Aws\S3\S3Client;
class S3{
    public $s3hd	= false;
    protected $CI;
    public function __construct(){
        $this->CI =& get_instance();
        //initialize s3 connection
        $this->CI->config->load('s3');

        $region = 'region_of_your_s3';
        
        if(!$this->s3hd) $this->s3hd = S3Client::factory(array(
            'credentials' => [
                'key'	=> $this->CI->config->item('access_key'),
                'secret'	=> $this->CI->config->item('secret_key')
            ],
            'version' => '2006-03-01',
            'region' => $region
        ));
    }

    public function listFile($Bucket=false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        $result = $this->s3hd->listObjects([
            'Bucket' => $Bucket
        ]);
        return $result;
    }

    public function getsBucket($Bucket=false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        $result = $this->s3hd->getBucket($Bucket);
        return $result;
    }

    public function url($name, $Bucket=false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        return $this->s3hd->getObjectUrl($Bucket, $name);
    }
    public function read($name, $Bucket = false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        if(!$this->exist($name, $Bucket)) exit("File not exist: $name");
        $info = $this->s3hd->getObject(array(
            'Bucket'       => $Bucket,
            'Key'          => $name,
        ));
        return $info['Body'];
    }

    public function setImage($name, $bucket= false){
        if(!$bucket) $Bucket = $this->CI->config->item('s3bucket');
        if(!$this->exist($name, $bucket)) exit("File not exist: $name");
        $info = $this->s3hd->create_object($bucket, $name, array(
            'contentType' => 'image/png'
        ));
        return $info;
    }

    public function del($name, $Bucket = false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        $info = $this->s3hd->deleteObject(array(
            'Bucket'       => $Bucket,
            'Key'          => $name,
        ));
        return $info;
    }

    public function exist($name, $Bucket = false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        return $this->s3hd->doesObjectExist($Bucket, $name);
    }
    public function upload($name, $file, $Bucket = false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        $result = $this->s3hd->putObject(array(
            'Bucket'       => $Bucket,
            'Key'          => $name,
            'SourceFile'   => $file,
            'ACL'          => 'public-read',
            //'StorageClass' => 'REDUCED_REDUNDANCY',
        ));
        $this->s3hd->waitUntil('ObjectExists', array(
            'Bucket' => $Bucket,
            'Key'    => $name,
        ));
        return $result;
    }

    public function write($name, $info, $Bucket = false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        $result = $this->s3hd->upload($Bucket, $name, $info, 'public-read');
        $this->s3hd->waitUntil('ObjectExists', array(
            'Bucket' => $Bucket,
            'Key'    => $name,
        ));
        return $result;
    }
    public function copyFile($src, $target, $Bucket = false){
        if(!$Bucket) $Bucket = $this->CI->config->item('s3bucket');
        $info = $this->s3hd->copyObject(array(
            'Bucket'       => $Bucket,
            'CopySource'   => $Bucket.'/'.$src,
            'Key'          => $target,
        ));
        return $info;
    }
}