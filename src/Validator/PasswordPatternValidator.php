<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordPatternValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordPattern) {
            throw new \InvalidArgumentException('Contrainte invalide pour PasswordPatternValidator.');
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
            return;
        }

        if (!preg_match('/[0-9]/', $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
