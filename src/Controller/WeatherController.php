<?php

namespace Drupal\weather\Controller;

use Drupal\Component\Utility\String;
use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\image\Entity;

/** 
 * Get a response code from any URL using Guzzle in Drupal 8!
 * 
 * Usage: 
 * In the head of your document:
 * 
 * use Drupal\weather\Http\CustomGuzzleHttp;
 * 
 * In the area you want to return the result, using any URL for $url:
 *
 * $check = new CustomGuzzleHttp();
 * $response = $check->performRequest($url);
 *  
 **/

class WeatherController extends ControllerBase {
  /**
   * IBP Catalog API URL.
   */
  const WEATHER_API_URL = 'http://api.worldweatheronline.com/free/v2/weather.ashx?q=chichester&key=ade5f8b9d77a3eb016b7515df6464&format=json';
  /**
   * IBP Catalog Connection Timeout.
   */
  const WEATHER_TIMEOUT = 10;

  /**
   * The catalog personal key.
   */
  protected $key;

  public function cache($CACHE_ID){
    $data = NULL;
    if ($cache = \Drupal::cache()->get($CACHE_ID)) {
      $data = $cache->data;
    }
    else {
      $data = 'dffI am now cached.';
      \Drupal::cache()->set($CACHE_ID, $data);
    }
    print_r($data);
    exit;
    return array($data);
  }

  /**
   * Generates an example page.
   */
  public function response() {
    // Create a HTTP client.
    $client = \Drupal::httpClient();

    // Create a request GET object.
    $response = $client->get(self::WEATHER_API_URL);
    #$request = $client->createRequest('GET', self::WEATHER_API_URL, []);
    if ($response->getStatusCode() == 200) {
      return json_decode($response->getBody(), TRUE);
    }
  }
  public function content() {
    $json_decoded = $this->response();
    if ($json_decoded) {
      $compt = 0;
      foreach ($json_decoded as $key => $weather) {
        $rows[$compt]['weatherDesc'] = $weather['current_condition'][0]['weatherDesc'][0]['value'];
        //$rows[$compt]['icon'] = '<img src="'.$weather['current_condition'][0]['weatherIconUrl'][0]['value'].'"/>';
        $rows[$compt]['temp'] = $weather['current_condition'][0]['temp_C'];
        $rows[$compt]['observation_time'] = $weather['current_condition'][0]['observation_time'];
        $compt++;
      }
      $compt = 0;
//      foreach ($json_decoded['data']['weather'][0]['hourly'] as $key => $hour) {
//        $rows[$compt]['weatherDesc'] = $weather['current_condition'][0]['weatherDesc'][0]['value'];
//        $rows[$compt]['temp'] = $weather['current_condition'][0]['temp_C'];
//        $rows[$compt]['observation_time'] = $weather['current_condition'][0]['observation_time'];
//        $compt++;
//      }
    }
    $table[] = array(
      '#type' => 'markup',
      '#markup' => '<h2>Current Conditions</h2>',
    );
    $table[] = array(
      '#type' => 'table',
      '#header' => array('Description', 'Temperature (C)' , 'Observation Time' ),
      '#rows' => $rows,
      '#attributes' => array('id' => 'current-conditions'),
      '#empty' => t('No item available.'),
    );
    return array('#markup' => drupal_render($table));
  }
}