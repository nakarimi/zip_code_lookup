<?php

namespace Drupal\zip_code_lookup\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a Zip Code constraint.
 *
 * @Constraint(
 *   id = "ZipCode",
 *   label = @Translation("Zip Code", context = "Validation"),
 * )
 */
class ZipCodeConstraint extends Constraint {

  public $errorMessage = 'Zip Code is not valid, please check length and format.';
  /**
   * {@inheritdoc}
   */
  public function validatedBy() {
    return '\Drupal\zip_code_lookup\Plugin\Validation\Constraint\ZipCodeValueValidator';
  }

}
