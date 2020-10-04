<?php

namespace App\Exception;

class PetNotFoundException extends \Exception {
  public function __construct($message)
  {
    parent::__construct($message, 404);
  }
}