<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;

use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use AppBundle\Form\Type\CredentialsType;

class AuthTokenController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     **/
    public function postAuthTokensAction(Request $request)
    {
        $logger      = $this->get('logger');
        $credentials = new Credentials();

        $form = $this->createForm(CredentialsType::class, $credentials);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $user          = $entityManager->getRepository('AppBundle:User')
            ->findOneByNickname($credentials->getLogin());

        if (!$user) {
            return $this->invalidCredentials();
        }

        $encoder         = $this->get('security.password_encoder');
        $isValidPassword = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isValidPassword) {
            return $this->invalidCredentials();
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \Datetime('now'));
        $authToken->setUser($user);

        $entityManager->persist($authToken);
        $entityManager->flush();

        return $authToken;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/auth-tokens/{id}")
     **/
    public function removeAuthTokenAction(Request $request)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $authToken     = $entityManager->getRepository('AppBundle:AuthToken')
            ->find($request->get('id'));

        $connectedUser = $this->get('security.token_storage')
            ->getToken()
            ->getUser();

        if ($authToken && $authToken->getUser()->getId() === $connectedUser->getId()) {
            $entityManager->remove($authToken);
            $entityManager->flush();
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException();
        }
    }

    private function invalidCredentials()
    {
        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException();
    }
}
