<?php
/**
 * Created by PhpStorm.
 * User: Katie Lacy
 * Date: 19/01/2016
 * Time: 14:55
 */
/**
 * @file
 * Contains \Drupal\weather\Form\WeatherForm.
 */

namespace Drupal\weather\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\weather\Http\weatherHttpRequest;
use Drupal\weather\Controller;

/**
 * Implements an example form.
 */
class WeatherForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weather_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $date = [
      0 => t('Today'),
      1 => t('Tomorrow'),
      2 => t('Enter Date'),
    ];
    $form['weather']['address'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Town / City'),
      '#attributes' => [
        'maxlength' => 50,
        'placeholder' => 'Enter a Town / City'
      ],
    ];

    $form['weather']['date'] = [
      '#type' => 'radios',
      '#title' => t('Date *'),
      '#options' => $date,
      '#required' => TRUE,
      '#default_value' => 0,
      '#description' => t('When a poll is closed, visitors can no longer vote for it.')
    ];
    $form['weather']['date_picker'] = [
      '#type' => 'date',
      '#default_value' => date('Y-m-d'),
    ];
    $form['weather']['days_to_fore'] = [
      '#title' => t('Days To Forecast'),
      '#type' => 'number',
      '#required' => TRUE,
      '#attributes' => [
        'max' => 10,
        'placeholder' => 'Enter days to forcast'
      ],
    ];
    $form['weather']['hourly_interval'] = [
      '#type' => 'number',
      '#required' => TRUE,
      '#title' => t('Hourly interval of reporting'),
      '#attributes' => [
        'max' => 24,
        'placeholder' => 'Enter hourly interval of report'
      ],
    ];
    $form['weather']['current_conditions'] = [
      '#type' => 'checkbox',
      '#title' => t('Show current conditions'),
    ];
    $form['weather']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#attributes' => [
        'class' => array('btn-block'),
      ]
    ];
    $form['#attributes']['class'][] = 'col-md-12';
    $form['#attached']['library'][] = 'weather/weather-form';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if (strlen($form_state->getValue('phone_number')) < 3) {
//      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $location = $values['address'];
    $date = $values['date'];
    $date_picker = $values['date_picker'];
    $days_to_fore = $values['days_to_fore'];
    $hourly_interval = $values['hourly_interval'];
    $current_conditions = $values['current_conditions'];

    if ($date_picker) {
      $date = $date_picker;
    }
    elseif ($date === 0) {
      $date = date('Y-m-d');
    }
    else {
      $datetime = new DateTime('tomorrow');
      $date = $datetime->format('Y-m-d');
    }
    $form_state->setRedirect('weather.cache' ,
      array(
        'date' => $date,
        'days_to_fore' => $days_to_fore,
        'hourly_interval' => $hourly_interval,
        'current_conditions' => $current_conditions,
        'location' => $location
      )
    );
  }

}
