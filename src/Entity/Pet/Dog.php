<?php

namespace App\Entity\Pet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Dog extends \App\Entity\Pet {
  protected function getHappinessPerStroke(): float
  {
    return 0.1;
  }

  public function getHappinessReduceRate(): float
  {
    return 0.2;
  }

  protected function getHungerPerFeed(): float
  {
    return 0.5;
  }

  public function getHungerIncreaseRate(): float
  {
    return 0.04;
  }
}