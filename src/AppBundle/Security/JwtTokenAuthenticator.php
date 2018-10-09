<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use AppBundle\Entity\User;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
	private $jwtEncoder;

    private $em;

	public function __construct(JWTEncoderInterface $jwtEncoder, EntityManager $em)
	{
		$this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
	}
	// getCredentials: read the Authorization header and return the token
	public function getCredentials(Request $request){
		$extractor = new AuthorizationHeaderTokenExtractor( 
			'Bearer',
			'Authorization'
		);
		$token = $extractor->extract($request);

		if (!$token) { 
			return;
		}
		return $token;
	}

	public function getUser($credentials, UserProviderInterface $userProvider) {
		// decode the token $data is now an array of whatever information we originally put into the token
		$data = $this->jwtEncoder->decode($credentials);

		if ($data === false) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        $username = $data['username'];

        return $this->em
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => $username]);
	}

	public function checkCredentials($credentials, UserInterface $user) {
		// no need to check password
		return true;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
	// TODO: Implement onAuthenticationFailure() method.
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
		// do nothing - let the controller be called
	}
	public function supportsRememberMe() {
		return false;
	}
}