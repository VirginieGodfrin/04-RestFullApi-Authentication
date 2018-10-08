<?php

namespace AppBundle\Controller\Api;

use AppBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends BaseController
{
    /**
     * @Route("/api/tokens")
     * @Method("POST")
     */
    public function newTokenAction(Request $request)
    {
    	// $request->getUser() don't work
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => 'weaverryan']);

        if (!$user) {
            throw $this->createNotFoundException();
        }
        // $request->getPassword() don't work
        $isValid = $this->get('security.password_encoder')
            ->isPasswordValid($user, 'I<3Pizza');

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode([
                'username' => $user->getUsername(),
                'exp' => time() + 3600
            ]);

        return new JsonResponse(['token' => $token]);
    }
}