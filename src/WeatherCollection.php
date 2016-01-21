<?php
/**
 *
 */
namespace Drupal\weather;



class WeatherCollection /*implements Iterator*/ {

  protected $collection = [];


  /**
   * {@inheritdoc}
   */
  public function addInterval(WeatherInterval $interval) {

  }
  /**
   * {@inheritdoc}
   */

  public function currentConditionTable($collection, $location) {
    $compt = 0;
    $rows = [];
    foreach ($collection['data']['current_condition'] as $key => $data) {
      $rows[$compt]['weatherDesc'] = $data['weatherDesc'][0]['value'];
      $rows[$compt]['temp_C'] = $data['temp_C'];
      $rows[$compt]['observation_time'] = $data['observation_time'];
      $compt++;
    }
    $table['current_condition'] = array(
      'header' => array('Description', 'Temperature (C)' , 'Observation Time' ),
      'rows' => $rows,
    );
    return $table;
  }
  /**
   * {@inheritdoc}
   */
  public function weatherTable($collection, $days) {
    $compt = 0;
    $rows = [];
    dpm($collection);

    foreach ($collection['data']['weather'][0]['hourly'] as $key => $data) {
      $rows[$compt]['time'] = $data['time'];
      $rows[$compt]['tempC'] = $data['tempC'];
      $rows[$compt]['weatherDesc'] = $data['weatherDesc'][0]['value'];
      $compt++;
    }
    $table = array(
      'header' => array('Description', 'Temperature (C)' , 'Observation Time' ),
      'rows' => $rows,
    );
    return $table;
  }
}