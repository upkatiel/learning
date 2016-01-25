<?php

namespace Drupal\weather;
use Symfony\Component\Validator\Constraints\DateTime;

class WeatherStorageCache implements WeatherStorageInterface {

  public function get(\DateTime $date, $location) {
    $location = strtolower(trim($location));
    $cid = implode(':', ['weather', $date->format('Y-m-d'), $location]);
    //return \Drupal::cache()->get($cid);
  }

  public function set(\DateTime $date, $location, WeatherCollection $weatherCollection) {
    $location = strtolower(trim($location));
    $cid = implode(':', ['weather', $date->format('Y-m-d'), $location]);
    return \Drupal::cache()->set($cid, $weatherCollection);
  }
}