<?php
namespace AppBundle\Repository;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Psr\Log\LoggerInterface;

use AppBundle\Entity\Gallery;
use AppBundle\Entity\Picture;

class GalleryRepository
{

    protected $finder;
    protected $filesystem;
    protected $userGalleriesPath;
    protected $logger;

    public function __construct($webDirectoryPath, LoggerInterface $logger)
    {
        $this->finder           = new Finder();
        $this->filesystem       = new Filesystem();
        $this->webDirectoryPath = $webDirectoryPath;
        $this->logger           = $logger;
    }

    public function getGallery($galleryName, $userNickname, $isCommonGallery = false)
    {
        $galleryPath = ($isCommonGallery)
            ? $this->webDirectoryPath . '/common/' . $galleryName
            : $this->webDirectoryPath . '/users/' . strtolower($userNickname) . '/' . $galleryName;

        $gallery     = new Gallery();
        $gallery->setName($galleryName);
        $gallery->setIsCommonGallery($isCommonGallery);

        if ($this->filesystem->exists($galleryPath)) {
            $this->finder->files()
                ->in($galleryPath)
                ->ignoreDotFiles(true);

            foreach ($this->finder as $file) {
                $picture = new Picture();
                $picture->setName($file->getFilename());
                $picture->setBase64Picture(
                    base64_encode(
                        file_get_contents($file->getRealPath())
                    )
                );

                $gallery->getPictures()->add($picture);
            }

            return $gallery;
        }
    }

    public function getAllUserGalleries($userNickname)
    {
        $userGalleriesPath = $this->webDirectoryPath . 'users/' . $userNickname;
        if ($this->filesystem->exists($userGalleriesPath)) {
            $this->finder->directories()
                ->in(array($userGalleriesPath, $this->webDirectoryPath . 'common'))
                ->ignoreDotFiles(true);

            $galleries = [];

            foreach ($this->finder as $gallery) {
                $galleryObject   = new Gallery();
                $pathArray       = explode('/', $gallery->getRealPath());
                $pathCount       = count($pathArray);
                $isCommonGallery = ($pathArray[$pathCount - 2] === "common")
                    ? true
                    : false;

                $galleryObject->setIsCommonGallery($isCommonGallery);
                $galleryObject->setName($pathArray[$pathCount - 1]);

                $lastUpdate = new \DateTime();
                $lastUpdate->setTimestamp($gallery->getMTime());

                $galleryObject->setLastUpdatedDate($lastUpdate);

                $galleries[] = $galleryObject;
            }

            return $galleries;
        }
    }

    public function initUserGalleries($userNickname)
    {
        if (!$this->filesystem->exists($this->webDirectoryPath . $userNickname)) {
            $this->createGallery(null, $userNickname, false);
        }
    }

    public function createGallery($galleryName, $userNickname, $isCommonGallery = false)
    {
        if ($isCommonGallery) {
            $galleryPath = $this->webDirectoryPath . '/common/' . $galleryName;
        } else {
            $galleryPath = $this->webDirectoryPath . '/users/' . $userNickname;

            if ($galleryName !== null) {
                $galleryPath .= '/' . $galleryName;
            }
        }

        if (!$this->filesystem->exists($galleryPath)) {
            try {
                $this->logger->info('Creating "' . $galleryPath . '" for user ' . $userNickname);
                $this->filesystem->mkdir($galleryPath);
            } catch (IOExceptionInterface $e) {
                $this->logger->critical('Could not create "' . $galleryName . '" directory in ' . $e->getPath());
                throw $e;
            }
        }
    }

    public function getLastModified($user)
    {
        $paths = [
            $this->webDirectoryPath . '/common/',
            $this->webDirectoryPath . '/users/' . strtolower($user->getNickname())
        ];

        $timestamp = 0;

        $this->finder->directories()
            ->in($paths)
            ->ignoreDotFiles(true);

        foreach ($this->finder as $dir) {
            $dirLastModified = $dir->getMTime();
            if ($dirLastModified > $timestamp) {
                $timestamp = $dirLastModified;
            }
        }

        $datetimeObj = new \Datetime();
        $datetimeObj->setTimestamp($timestamp);

        return $datetimeObj;
    }
}
?>
