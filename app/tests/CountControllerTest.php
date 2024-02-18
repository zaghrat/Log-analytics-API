<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CountControllerTest extends WebTestCase
{
    public function testCountWithNoParametersAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('CountItem', $responseData);
        $this->assertArrayHasKey('counter', $responseData['CountItem']);
        $this->assertGreaterThan(0, $responseData['CountItem']['counter']);
    }

    public function testCountWithBadParametersAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count', [
            'statusCode' => 123
        ]);


        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertIsArray($responseData['errors']);
        $this->assertJsonStringEqualsJsonString($client->getResponse()->getContent(), '{"errors":["Parameter statusCode must be a valid HTTP status code."]}');
    }


    public function testCountWithRightParametersAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/count', [
            'statusCode'    =>  400,
            'startDate'     =>  '2018-08-17',
            'endDate'       =>  '2018-08-18'
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('CountItem', $responseData);
        $this->assertArrayHasKey('counter', $responseData['CountItem']);
        $this->assertGreaterThan(4, $responseData['CountItem']['counter']);
    }
}
