<?php

namespace NetBS\CoreBundle\Validator\Constraints;

use NetBS\CoreBundle\Validator\Validators\UserValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class User extends Constraint
{
    public $rule    = "";
    public $key     = "user";
    public $message = "You're not allowed to change this value";

    public function validatedBy()
    {
        return UserValidator::class;
    }
}