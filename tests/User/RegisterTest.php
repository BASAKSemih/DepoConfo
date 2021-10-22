<?php

namespace App\Tests\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class RegisterTest extends WebTestCase
{
    public function testSuccessfulRegistration(): void
    {
        $client = static::createClient();
        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");
        $crawler = $client->request(Request::METHOD_GET, $router->generate('security_register'));

        $form = $crawler->filter("form[name=user]")->form([
            "user[firstName]" => "John",
            "user[lastName]" => "Doe",
            "user[email]" => "john@doe.com",
            "user[password][first]" => "password",
            "user[password][second]" => "password"

        ]);
        $client->submit($form);
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
