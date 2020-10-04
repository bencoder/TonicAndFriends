<?php

namespace App\Exception;

class InvalidInputException extends \Exception {
  public function __construct($message)
  {
    parent::__construct($message, 400);
  }
}