<?php

namespace Tests\AppBundle\Controller\Api;

use AppBundle\Test\ApiTestCase;


class TokenControllerTest extends ApiTestCase
{
	public function testPOSTCreateToken()
	{
		// we need a user in the dataBase
		$this->createUser('weaverryan', 'I<3Pizza');

		// the POST request
		$response = $this->client->post('/api/tokens', [ 
			// To send an HTTP Basic username and password with Guzzle, 
			// add an auth option and set it to an array containing the username and password
			'auth' => ['weaverryan', 'I<3Pizza']
		]);

		$this->assertEquals(200, $response->getStatusCode());

		$this->asserter()->assertResponsePropertyExists(
			$response,
			'token'
		);
	}

}