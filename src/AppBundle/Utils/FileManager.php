<?php


namespace AppBundle\Utils;


class FileManager
{
    private $rootDir;

    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function fileManager($post)
    {
        if ($post->getImageFile() !== null) {
            $file = $post->getImageFile();
            $fileName = 'images/post/' . md5(uniqid()) . '.' . $file->guessExtension();
            $imagesDir = $this->rootDir . '/../web/images/post';
            $file->move($imagesDir, $fileName);
            $post->setImageName($fileName);
        }
        return ($post);
    }
}