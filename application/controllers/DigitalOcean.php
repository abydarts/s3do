<?php
/**
 * Created by PhpStorm.
 * User: Win_10
 * Date: 28/02/2018
 * Time: 9:27
 */

class DigitalOcean extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->bucket = "your_bucket" ;
        $this->load->library("CloudStorage", NULL, "s3");
        $this->load->helper("form");
    }
    public function upload_file(){
         if (!empty($_FILES) && $_FILES["file"]["error"] != 4) {
            $files = $_FILES["file"]["name"];
            $ext = $ext = pathinfo($files, PATHINFO_EXTENSION);
            $fileName = "folder_in_bucket/file_name_".time().".$ext"; //random filename with time
            $tmpName = $_FILES["file"]["tmp_name"];
            if ($this->s3->upload($fileName, $tmpName, $this->bucket)) {
                $this->session->set_flashdata("success", "Berhasil upload file");
                redirect(site_url("amazon/lihat_file"));
            } else {
                $this->session->set_flashdata("error", "Gagal upload file");
                redirect("amazon/upload_file");
            }
        }
        $this->load->view("upload_file");
    }

    public function lihat_file() {
        $data["list"] = $this->s3->listFile($this->bucket);
        $data['files'] = $this->s3->url("folder_in_bucket/file_name");
        $this->load->view('lihat_file', $data);
    }
}