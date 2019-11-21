<?php

define("TARGETDIR","../uploads/");
define("DBDIR","uploads/");

class Media
{
    private $file = null;
    private $userId = null;
    public $mediaType = null;
    public $err_message = null;
    public function __construct($file,$userId)
    {
        $this->userId = $userId;
        $this->file = $file;
        $this->mediaType = strtolower(pathinfo($this->file["name"], PATHINFO_EXTENSION));
    }
    public  function upload(){
        $file = $this->targetFile();;
        $targetFile = TARGETDIR.$file;
        $dbloc = DBDIR.$file;
        if(file_exists($targetFile)){
            $file = $this->targetFile($this->userId."_");
            $targetFile = TARGETDIR.$file;
            $dbloc = DBDIR.$file;
        }
        if($this->mediaType != "jpg" && $this->mediaType != "png" && $this->mediaType != "jpeg"  && $this->mediaType != "mp4" && $this->mediaType != "mp3"
        && $this->$this->mediaType != "3gpp" && $this->mediaType != "quicktime" && $this->mediaType != "amr" && $this->mediaType != "wav") {

            $this->err_message = "Sorry, only JPG, JPEG, PNG, MP4 and MP3 files are allowed.";
            return false;
        }
        if (move_uploaded_file($this->file["tmp_name"], $targetFile)) {
            return $dbloc;
        } else {
            $this->err_message = "Sorry, there was an error uploading your file.";
            return false;
        }
    }
    private function targetFile($prefix = ""){
        return $prefix.strval(microtime(true)).".".$this->mediaType;
    }





}