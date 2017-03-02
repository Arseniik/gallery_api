<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;

class UserController extends Controller
{

    /**
     * @Rest\View()
     * @Rest\Get("/users")
     **/
    public function getUsersAction (Request $request)
    {
        try {
            $users = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->findAll();
        } catch (Exception $e) {
            return View::create(['message' => 'Internal server error' . PHP_EOL . 'Stacktrace : ' . $e->getMessage()], Response::INTERNAL_SERVER_ERROR);
        }

        if (empty($users)) {
            throw new NotFoundHttpException('No user found');
        }

        return $users;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}")
     **/
    public function getUserAction (Request $request)
    {
        $id = $request->get('id');

        try {
            $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($id);
        } catch (Exception $e) {
            return View::create(['message' => 'Internal server error' . PHP_EOL . 'Stacktrace : ' . $e->getMessage()], Response::INTERNAL_SERVER_ERROR);
        }

        if (empty($user)) {
            return $this->noUserFound($id);
        }

        return $user;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/admin/users")
     **/
    public function postUserAction (Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user, ['validation_groups' => ['Default', 'New']]);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            try {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encoded);

                $entityManager = $this->get('doctrine.orm.entity_manager');
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (Exception $e) {
                return View::create(['message' => 'Internal server error' . PHP_EOL . 'Stacktrace : ' . $e->getMessage()], Response::INTERNAL_SERVER_ERROR);
            }

            $galleryRepository = $this->get('gallery.repository');
            $galleryRepository->initUserGalleries(
                strtolower($user->getUsername())
            );

            return $user;
        }

        return $form;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/users/{id}")
     **/

    public function deleteUserAction (Request $request)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $user = $entityManager->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/users/{id}")
     **/
    public function putUserAction (Request $request)
    {
       return $this->updateUser($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/users/{id}")
     **/
    public function patchUserAction (Request $request)
    {
       return $this->updateUser($request, false);
    }

    /**
     * Update fully or partially a user
     * @param Request $request
     * @param boolean $isFullUpdate
     **/
    private function updateUser(Request $request, $isFullUpdate)
    {
        $userId        = $request->get('id');
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $user          = $entityManager->getRepository('AppBundle:User')->find($userId);

        if (empty($user)) {
            return $this->noUserFound($userId);
        }

        $options = [];

        if ($isFullUpdate) {
            $options = ['Default', 'FullUpdate'];
        }

        $form = $this->createForm(UserType::class, $user, $options);
        $form->submit($request->request->all(), $isFullUpdate);

        if ($form->isValid()) {
            $plainPassword = $user->getPlainPassword();

            if (!empty($plainPassword)) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $plainPassword);
                $user->setPassword($encoded);
            }

            $entityManager->merge($user);
            $entityManager->flush();

            return $user;
        }

        return $form;
    }

    private function noUserFound($id)
    {
        throw new NotFoundHttpException('User with ID ' . $id . ' not found');
    }
}
?>
