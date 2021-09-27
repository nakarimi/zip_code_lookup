<?php

namespace Drupal\zip_code_lookup\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use CommerceGuys\Addressing\AddressFormat\AddressField;

/**
 * Validates the Zip Code constraint.
 */
class ZipCodeValueValidator extends ConstraintValidator
{

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint)
  {
    foreach ($items as $item) {
      if ($errorMessage = $this->validateZip($item)) {
        $this->context->buildViolation($errorMessage)
        ->atPath('[postal_code]')
        ->addViolation();
        \Drupal::logger('Violation')->error($errorMessage,[$item]);
      }
    }
  }


  private function validateZip($value)
  {
    $api = "CityStateLookup";
    $uid = "101WEMBA0709";
    $zip = $value->postal_code;
    $state = $value->administrative_area;
    $zip = "00000";
    $xml = '<CityStateLookupRequest USERID="'.$uid.'">
    <ZipCode ID="0">
    <State>'.$state.'</State>
    <Zip5>'.$zip.'</Zip5>
    </ZipCode>
    </CityStateLookupRequest>';
    $url = "https://secure.shippingapis.com/ShippingAPI.dll?API=$api&XML=$xml";
    $request = \Drupal::httpClient()->request('GET', $url, []);
    $response = $request->getBody()->getContents(); //You would like JSON decode the response.
    $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    $response = json_decode($json,TRUE);
    $response = reset($response);
    $error = (isset($response['Error'])) ? $response['Error'] : FALSE;
    if ($error) {
      return $error['Description'];
    }
    if($response['State'] == $state) {
      return "State and Zip Code don't match!";
    }
    return FALSE;
  }
}
