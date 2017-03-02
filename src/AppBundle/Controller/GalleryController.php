<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;

use AppBundle\Entity\Gallery;
use AppBundle\Form\Type\GalleryType;
use AppBundle\Controller\RestController;

class GalleryController extends RestController implements Cacheable
{

    /**
     * @Rest\View()
     * @Rest\Post("/galleries")
     **/
    public function postGalleryAction (Request $request)
    {
        $gallery         = new Gallery();
        $galleryName     = $request->get('name');
        $isCommonGallery = $request->get('isCommonGallery');
        $galleryRepository  = $this->get('gallery.repository');
        $user            = $this->getUser();

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $galleryRepository->createGallery($galleryName, $user->getNickname(), $isCommonGallery);

            return $gallery;
        }

        return $form;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/galleries")
     **/
    public function getGalleriesAction (Request $request)
    {
        $user           = $this->getUser();
        $galleryRepository = $this->get('gallery.repository');

        $galleries      = $galleryRepository->getAllUserGalleries(strtolower($user->getNickname()));

        return new JsonResponse($galleries, Response::HTTP_OK);
    }

    /**
     * @Rest\View()
     * @Rest\Get("/galleries/{name}")
     **/
    public function getGalleryAction (Request $request)
    {
        $user              = $this->getUser();
        $galleryRepository = $this->get('gallery.repository');
        $gallery           = $galleryRepository->getGallery(
            $request->get('name'),
            $user->getNickname(),
            $request->get('isCommon')
        );

        $test = $galleryRepository->getLastModified($user);

        return new JsonResponse($gallery, Response::HTTP_OK);
    }

    public function getLastModifiedDate()
    {
        $user         = $this->getUser();
        $lastEditDate = $this->get('gallery.repository')->getLastModified($user);

        if ($lastEditDate instanceof \DateTime) {
            return $lastEditDate;
        }

        return new \DateTime();
    }
}
?>
