<?php

namespace FOS\UserBundle\Tests\Form\Type;

use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ValidatorExtensionTypeTestCase
 * FormTypeValidatorExtension added as default. Useful for form types with `constraints` option
 *
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
class ValidatorExtensionTypeTestCase extends TypeTestCase
{
    protected function getTypeExtensions()
    {
        if (interface_exists('Symfony\Component\Validator\Validator\ValidatorInterface')) {
            $validator = $this->getMock('Symfony\Component\Validator\Validator\ValidatorInterface');
        } else {
            $validator = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        }

        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));

        return array(
            new FormTypeValidatorExtension($validator),
        );
    }
}
