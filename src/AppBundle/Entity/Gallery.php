<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Gallery
{
    protected $name;
    protected $pictures;
    protected $isCommonGallery;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getPictures()
    {
        return $this->pictures;
    }

    public function setPictures($pictures)
    {
        $this->pictures = $pictures;
    }

    public function isCommonGallery()
    {
        return $this->isCommonGallery;
    }

    public function setIsCommonGallery($isCommonGallery)
    {
        $this->isCommonGallery = $isCommonGallery;
    }
}
?>
