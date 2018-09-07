<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader 
{
    private $targetDirectory;
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $filename = md5(uniqid() . mt_rand(100000,999999)) . '.' . $file->guessExtension();
        $file->move($this->getTargetDirectory(),$filename);
        return $filename;
    }
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}