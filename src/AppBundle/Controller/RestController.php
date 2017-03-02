<?php
namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

abstract class RestController extends Controller
{
    protected $lastModifiedResponse;

    public function isNotModified(\DateTime $lastModified, Request $request)
    {
        $this->lastModifiedResponse = new Response();
        $this->lastModifiedResponse->setLastModified($lastModified);
        $this->lastModifiedResponse->setPublic();

        if ($this->lastModifiedResponse->isNotModified($request)) {
            return true;
        }

        return false;
    }

    public function getLastModifiedResponse()
    {
        return $this->lastModifiedResponse;
    }
}
