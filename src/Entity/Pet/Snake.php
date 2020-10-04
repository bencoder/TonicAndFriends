<?php

namespace App\Entity\Pet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Snake extends \App\Entity\Pet {
  protected function getHappinessPerStroke(): float
  {
    return 0.01;
  }

  public function getHappinessReduceRate(): float
  {
    return 0.01;
  }

  protected function getHungerPerFeed(): float
  {
    return 1;
  }

  public function getHungerIncreaseRate(): float
  {
    return 0.01;
  }
}