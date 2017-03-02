<?php
namespace AppBundle\Entity;

class Picture
{
    protected $name;
    protected $base64Picture;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getBase64Picture()
    {
        return $this->base64Picture;
    }

    public function setBase64Picture($base64Picture)
    {
        $this->base64Picture = $base64Picture;
    }
}
?>
