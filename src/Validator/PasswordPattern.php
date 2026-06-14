<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PasswordPattern extends Constraint
{
    public string $message = 'Le mot de passe doit contenir au moins une majuscule et un chiffre.';
}
