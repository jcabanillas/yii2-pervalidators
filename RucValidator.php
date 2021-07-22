<?php

/**
 * @author     Javier Cabanillas <jcabanillas@bitevolution.net>
 * @copyright  2017 Javier Cabanillas.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.1
 * @link       https://github.com/jcabanillas/yii2-pervalidators
 */

namespace jcabanillas\pervalidators;

use yii\validators\Validator;
use yii\base\ErrorException;

/**
 * Valida que la cadena sea un RUC válido según las leyes peruanas.
 *
 * Validates that the string is a valid RUC according to Peru law.
 */
class RucValidator extends Validator
{

    /**
     * Determina si se convierte el campo a mayúsculas automáticamente.
     * @var boolean determines if the field is automatically converted to uppercase.
     */
    public $toUpper = true;

    const VALIDCHARS = '0123456789';

    private $errors = array();

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->message === null) {
            $this->message = \Yii::t('validator', '{attribute} must be a string.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        //Uppercase the value (all RUC must be uppercase).
        if ($this->toUpper === true) {
            $model->{$attribute} = strtoupper($model->{$attribute});
        }
        $value = $model->{$attribute};
        if (!$this->_validateRuc($value)) {
            $this->addError($model, $attribute, \Yii::t('validator', 'RUC "{ruc}" is invalid.', array('ruc' => $value)));
            foreach ($this->errors as $error) {
                $this->addError($model, $attribute, $error);
            }
        }
    }

    private function _validateRuc($value)
    {
        // Check lenght
        // Peru RUC must be 12 or 13 chars long.
        //  RUC for companies or businnesses is 12 chars long
        //  RUC for persons is 13 chars long.
        switch (strlen($value)) {
            case 11:
                $datePosition = 1;
                break;
            default:
                $this->errors[] = \Yii::t('validator', 'Must be 11 characters long');
                return false;
                break;
        }

        // Check for valid characters
        for ($i = 0; $i < strlen($value); $i++) {
            if (strpos(self::VALIDCHARS, $value[$i]) === false) {
                $this->errors[] = \Yii::t('validator', 'Character "{char}" is not allowed.', array('char' => $value[$i]));
            }
        }

        // RUC consists of:
        //  Cada contribuyente es identificado con un número de 11 dígitos al cual se le denomina número RUC. Este número es individual, irrepetible y permanente, y debe utilizarse en cualquier trámite ya sea con entidades públicas o privadas.
        //
        //  La SUNAT forma los números RUC de la siguiente forma:
        //
        //  Personas Naturales: El número RUC inicia con el número “10”. Los números siguientes son los 8 dígitos del Documento Nacional de Identidad (DNI).
        //  Personas Jurídicas: El número RUC inicia con el número “20”. Los números siguientes en un número correlativo asignado por el sistema de la SUNAT.
        //  Los contribuyentes identificados con otros documentos de identidad como carnet de fuerzas armadas o de extranjería, el número RUC inicia con los números “15” y “17”.
        $part1 = substr($value, 0, $datePosition);
        $part2 = substr($value, $datePosition, 9);

        if ($part1 != '10' && $part1 != '20') {
            $this->errors[] = \Yii::t('validator', '"{part1}" must be 10 or 20.', array('part1' => $part1));
        }

        return (count($this->errors) == 0);
    }

}
