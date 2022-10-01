<?php

namespace App\service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    public function upload(UploadedFile $file,$directory,$path)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $main_path = "/".$path.'/'.$fileName;
        $file->move($directory.'/'.$path,$fileName);
        return $main_path;
    }
    public function update(UploadedFile $file,$directory,$path,$last)
    {
        $this->remove($last);
        return $this->upload($file,$directory,$path);
    }

    public function remove($path)
    {
        $fileSystem = new Filesystem();
        $main_path = "./uploads".$path;
        if ($fileSystem->exists($main_path))
        {
            $fileSystem->remove($main_path);
        }
    }

}