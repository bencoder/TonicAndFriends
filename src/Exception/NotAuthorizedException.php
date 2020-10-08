<?php

namespace App\Exception;

class NotAuthorizedException extends \Exception {
  public function __construct($message)
  {
    parent::__construct($message, 403);
  }
}