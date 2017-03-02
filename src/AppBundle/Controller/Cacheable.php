<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

interface Cacheable
{

    public function isNotModified(\DateTime $lastModified, Request $request);

    public function getLastModifiedDate();

    public function getLastModifiedResponse();
}
?>
