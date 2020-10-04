<?php

namespace App\Entity\Pet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Cat extends \App\Entity\Pet {
  protected function getHappinessPerStroke(): float
  {
    return 0.05;
  }

  public function getHappinessReduceRate(): float
  {
    return 0.1;
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