<?php

namespace App\Test\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PetControllerTest extends WebTestCase
{
  public function testPetMethods()
  {
    $client = static::createClient([], [
      'HTTP_USER_ID' => 1
    ]);

    //test creating the pet:
    $client->request('POST', '/pet', [
      'type' => 'cat',
      'name' => 'test-cat'
    ]);
  
    $this->assertEquals(200, $client->getResponse()->getStatusCode());
    $pet = json_decode($client->getResponse()->getContent());

    $this->assertEquals('test-cat', $pet->name);
    $this->assertEquals('cat', $pet->type);
    $this->assertEquals(0.5, $pet->happiness);
    $this->assertEquals(0.5, $pet->hunger);

    //test getting a single pet:
    $client->request('GET', "/pet/{$pet->id}");
    $pet2 = json_decode($client->getResponse()->getContent());
    $this->assertEquals($pet, $pet2);

    //Test listing all the pets:
    $client->request('GET', "/pet");
    $petList = json_decode($client->getResponse()->getContent());
    $this->assertIsArray($petList);
    $this->assertCount(1, $petList);

    //Test feeding the pet:
    $client->request('POST', "/pet/{$pet->id}/feed");
    $petAfterFeed = json_decode($client->getResponse()->getContent());
    $this->assertLessThan($pet->hunger, $petAfterFeed->hunger, 'Hunger is less after feeding');

    //Test stroking the pet:
    $client->request('POST', "/pet/{$pet->id}/stroke");
    $petAfterStroke = json_decode($client->getResponse()->getContent());
    $this->assertGreaterThan($pet->happiness, $petAfterStroke->happiness, 'Happiness is greater after stroke');

    //Test that another user cannot access the pet:
    $client->setServerParameters([
      'HTTP_USER_ID' => '2'
    ]);
    $client->request('GET', "/pet/{$pet->id}");
    $this->assertEquals(403, $client->getResponse()->getStatusCode());
  }


}