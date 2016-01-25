<?php

namespace Drupal\weather;
interface WeatherStorageInterface {
  public function get(\DateTime $date, $location);
  public function set(\DateTime $date, $location, WeatherCollection $weather);
}