<?php
/**
 *
 */
namespace Drupal\weather;



class WeatherCollection implements \Iterator {

  private $iterator = 0;
  protected $collection = [];
  public $current = [];

  public function addInterval(WeatherInterval $interval) {
    $interval_date = $interval->getDate();
    $date = $interval_date->format('Y-m-d');
    $time = $interval_date->format('H:i');
    $this->collection[$date . '-' . $time] = $interval;
  }
  public function currentConditions(WeatherInterval $interval) {
    $interval_date = new \DateTime('now');
    $date = $interval_date->format('Y-m-d');
    $time = $interval_date->format('H:i');
    $this->current[$date . '-' . $time] = $interval;
    return $this->current[$date . '-' . $time];
  }


  public function rewind() {
    $this->iterator -= 1;
  }

  public function next() {
    $this->iterator += 1;
  }

  public function current() {
    $this->collection[$this->iterator];
  }

  public function valid() {
    return isset($this->collection[$this->iterator]);
  }

  public function key() {
    return $this->iterator;
  }

  public function getCollection() {
    return $this->collection;
  }
}
