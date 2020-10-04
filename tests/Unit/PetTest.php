<?php

namespace App\Test\Unit;

use App\Entity\Pet;
use App\Util\DateTimeProvider;
use DateTime;
use PHPUnit\Framework\TestCase;

class PetTest extends TestCase
{
  public function testFeeding()
  {
    DateTimeProvider::$dateOverride = new DateTime("2020-01-01 00:00:00");
    $pet = new TestPet(1, "Test Pet");
    $this->assertEquals($pet->getHunger(), 0.5);  //Should default to 0.5

    DateTimeProvider::$dateOverride = new DateTime("2020-01-01 01:00:00"); //move time forward by 1 hour
    $this->assertEquals($pet->getHunger(), 0.6);  //Hunger rate is 0.1 so it should be 0.6 now
    $pet->feed();
    $this->assertEquals($pet->getHunger(), 0.4);  //Feeding should make hunger go down by 0.2

    $pet->feed();
    $pet->feed();
    $pet->feed();
    $pet->feed();
    $this->assertEquals($pet->getHunger(), 0);  //The hunger should not go below 0

    DateTimeProvider::$dateOverride = new DateTime("2020-01-02 00:00:00"); //move time forward by 1 day
    $this->assertEquals($pet->getHunger(), 1);  //The hunger should not go above 1
  }

  public function testStroking()
  {
    DateTimeProvider::$dateOverride = new DateTime("2020-01-01 00:00:00");
    $pet = new TestPet(1, "Test Pet");
    $this->assertEquals($pet->getHappiness(), 0.5);  //Should default to 0.5

    DateTimeProvider::$dateOverride = new DateTime("2020-01-01 01:00:00"); //move time forward by 1 hour
    $this->assertEquals($pet->getHappiness(), 0.2);  //Happiness rate is 0.3 so .5 - .3 = .2
    
    $pet->stroke();
    $this->assertEquals($pet->getHappiness(), 0.6);  //Stroking should add .4

    $pet->stroke();
    $pet->stroke();
    $pet->stroke();
    $this->assertEquals($pet->getHappiness(), 1);  //Happiness should not go above 1

    DateTimeProvider::$dateOverride = new DateTime("2020-01-02 00:00:00"); //move time forward by 1 day
    $this->assertEquals($pet->getHappiness(), 0);  //Happiness should not go below 0
  }
}

class TestPet extends Pet
{
  public function getHungerIncreaseRate(): float
  {
    return 0.1;
  }
  protected function getHungerPerFeed(): float
  {
    return 0.2;
  }
  public function getHappinessReduceRate(): float
  {
    return 0.3;
  }
  protected function getHappinessPerStroke(): float
  {
    return 0.4;
  }
}