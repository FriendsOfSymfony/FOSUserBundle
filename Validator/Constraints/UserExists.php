<?php

namespace FOS\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UserExists extends Constraint
{
    public $message = 'fos_user.resetting.request.invalid_username';

    public function validatedBy()
    {
        return 'fos_user_exists_validator';
    }
}
