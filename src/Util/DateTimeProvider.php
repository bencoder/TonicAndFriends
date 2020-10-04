<?php

namespace App\Util;

class DateTimeProvider 
{
  //Set this property in order to return the given DateTime instead of a new current DateTime
  static ?\DateTimeInterface $dateOverride = null;

  static function getCurrentDate()
  {
    return self::$dateOverride ?? new \DateTime();
  }
}