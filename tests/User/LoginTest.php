<?php

namespace App\Tests\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class LoginTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");
        $crawler = $client->request(Request::METHOD_GET, $router->generate("app_login"));
        $form = $crawler->filter("form[name=login]")->form([
            "email"  => 'john@doe.com',
            "password" => 'password'
        ]);
        $client->submit($form);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
