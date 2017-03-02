<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetUsers()
    {
        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/users',
            [],
            [],
            [
                'X-Auth-Token' => '',
            ]);
    }
}
?>
