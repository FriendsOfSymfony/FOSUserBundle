<?php

namespace FOS\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PasswordNotAlreadyRequested extends Constraint
{
    public $message = 'resetting.password_already_requested';

    public function validatedBy()
    {
        return 'fos_password_not_already_requested_validator';
    }
}
