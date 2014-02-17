<?php

namespace FOS\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UserExists extends Constraint
{
    public $message = 'resetting.request.invalid_username';

    public function validatedBy()
    {
        return get_class($this) . 'Validator';
    }
}
