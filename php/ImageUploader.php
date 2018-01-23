<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image upload service.
 *
 * Used to upload logotypes, profile avatars & others images.
 *
 * @package AppBundle\Service
 */
class ImageUploader
{
    /** @var string */
    private $rootDir;

    /** @var string */
    private $dir;

    /**
     * ImageUploader constructor.
     *
     * @param string $rootDir
     * @param string $dir
     */
    public function __construct(string $rootDir, string $dir)
    {
        $this->rootDir = $rootDir;
        $this->dir = $dir;
    }

    /**
     * Upload image.
     *
     * @param UploadedFile $file
     * @param null|string $subDir
     *
     * @return string
     */
    public function upload(UploadedFile $file, string $subDir = null)
    {
        $targetDir = $subDir === null ? $this->getTargetDir() : $this->getTargetDir() . '/' . $subDir;

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($targetDir, $fileName);

        $uploadDir = $this->getDir() . '/' . $subDir;

        return $uploadDir . '/' . $fileName;
    }

    /**
     * Crop uploaded image.
     *
     * @param string $file
     * @param string $type
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h
     *
     * @return bool
     */
    public function crop(string $file, string $type = 'image/jpeg', int $x = 0, int $y = 0, int $w = 0, int $h = 0)
    {
        $image = $type === 'image/png' ? imagecreatefrompng($file) : imagecreatefromjpeg($file);

        $cropped = imagecrop($image, [
            'x' => $x,
            'y' => $y,
            'width' => $w,
            'height' => $h,
        ]);

        $to = $this->rootDir . '/../web/' . $file;

        return $type === 'image/png' ? imagepng($cropped, $to) : imagejpeg($cropped, $to);
    }

    /**
     * Remove uploaded file.
     *
     * @param string $filePath
     * @return bool
     */
    public function remove(string $filePath)
    {
        if (empty($filePath)) {
            return false;
        }

        $file = $this->rootDir . '/../web/' . $filePath;

        return file_exists($file) ? unlink($file) : false;
    }

    /**
     * Gets image directory.
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Gets upload target directory.
     *
     * @return string
     */
    public function getTargetDir()
    {
        return $this->rootDir . '/../web/' . $this->dir;
    }
}
