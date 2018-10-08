<?php

namespace NetBS\CoreBundle\Validator\Validators;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserValidator extends ConstraintValidator
{
    private $storage;

    private $engine;

    public function __construct(TokenStorage $storage)
    {
        $this->storage  = $storage;
        $this->engine   = new ExpressionLanguage();
    }

    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value)
            return;

        $key    = $constraint->getDefaultOption();
        $user   = $this->storage->getToken()->getUser();

        if(!$user || !$this->engine->evaluate($constraint->rule, [$constraint->key  => $user]))
            $this->context->buildViolation($constraint->message)->addViolation();
    }
}