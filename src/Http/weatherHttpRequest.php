<?php

namespace Drupal\weather\Http;

use Drupal\Component\Utility\String;
use GuzzleHttp\Exception\RequestException;

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

class weatherHttpRequest {
  /**
   * IBP Catalog API URL.
   */
  const WEATHER_API_URL = 'http://www.google.com';

  /**
   * IBP Catalog Connection Timeout.
   */
  const WEATHER_TIMEOUT = 10;

  /**
   * The catalog personal key.
   */
  protected $key;

  /**
   * Generates an example page.
   */
  public function content() {

    // Get module configuration.
    $module_config = \Drupal::config('weather.settings');
    $this->key = $module_config->get('key');

    // Create a HTTP client.
    $client = \Drupal::httpClient();

    // Set default options.
    $client->setDefaultOption('timeout', self::WEATHER_TIMEOUT);

    // Create a request GET object.
    $request = $client->createRequest('GET', self::WEATHER_API_URL);

    // Filter on Key.
    if ($this->key != '') {
      $filter = "SecureGuid eq '" . $this->key . "'";
    } else {
      drupal_set_message('Set up you IBP Catalog key', 'status', TRUE);
      return FALSE;
    }

    // Add a few query strings.
    $query = $request->getQuery();
    $query->set('$filter', $filter);

    try {
      $response = $client->send($request);
    } catch (RequestException $e) {
      drupal_set_message('Bad request', 'error', TRUE);
      return FALSE;
    } catch (\Exception $e) {
      drupal_set_message('Bad request', 'error', TRUE);
      return FALSE;
    }

    // If success.
    if ($response->getStatusCode() == 200) {

      // We are expecting XML content.
      $xml = $response->xml();

      $compt = 0;
      foreach($xml->xpath('//m:properties') as $properties) {
        $d = $properties->children('http://schemas.microsoft.com/ado/2007/08/dataservices');
        $rows[$compt]['url'] = String::checkPlain($d->CalculatedUrl);
        $rows[$compt]['language'] = String::checkPlain($d->Language);
        $rows[$compt]['subcategoryfr'] = String::checkPlain($d->SubCategoryFR);
        $rows[$compt]['friendlysizekey'] = String::checkPlain($d->FriendlySizeKey);
        $rows[$compt]['productdomaincodedescriptionfr'] = String::checkPlain($d->ProductDomainCodeDescriptionFR);
        $rows[$compt++]['companyname'] = String::checkPlain($d->CompanyName);
      }

    }

    $header = array('URL', 'Language', 'Category Description', 'FriendlySizeKey', 'Product', 'Company');

    $table = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => array('id' => 'book-outline'),
      '#empty' => t('No item available.'),
    );
    return drupal_render($table);
  }
}