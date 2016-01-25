<?php
namespace Drupal\weather;

use Drupal\Core\Datetime\Entity\DateFormat;
use Symfony\Component\Validator\Constraints\DateTime;

class WeatherInterval {

  protected $date;
  protected $collection;

  public $temp;
  public $visibility;
  public $weatherIconUrl;
  public $dateTime;

  public function __construct($date, array $config = []) {
    $date = is_object($date) ? $date : new \DateTime($date);
    $this->date = $date;
    foreach($config as $config_key => $config_value) {
      if (method_exists($this, 'set' . ucfirst($config_key))) {
        call_user_func(array($this, 'set' . ucfirst($config_key)), $config_value);
      }
    }
  }

  public function setTemp($temp){
   $this->temp = $temp;
  }

  public function setvisibility($visibility){
   $this->visibility = $visibility;
  }

  public function setweatherIconUrl($weatherIconUrl){
   $this->weatherIconUrl = $weatherIconUrl;
  }

  public function setdateTime($dateTime){
   $this->dateTime = $dateTime;
  }

  public function getDate(){
    return $this->date;
  }

  public function format($format){
    return $this->date->format($format);
  }
}