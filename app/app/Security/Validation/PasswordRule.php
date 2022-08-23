<?php

namespace App\Security\Validation;

class PasswordRule
{
    public function validate(string $field, string $value, array $params, array $fields): bool
    {

        if($this->caseCheck($value) === false){
            return false;
        }

        if($this->hasNumber($value) === false){
            return false;
        }

        if($this->hasSpecialCharacter($value) === false){
            return false;
        }

        if($this->checkIfContainsItOrReverseOfIt($value, $params[0], $params[1]) === false){
            return false;
        }

        return true;
    }

    /* returns false if password is only uppercase or only lowercase */
    /* BONUS: if there are only numbers/special characters in the password string, the string remains unchanged
              when strtoupper() or strtolower() are applied, therefore throwing an error anyway */
    public function caseCheck(string $password): bool
    {
        if (strtolower($password) === $password) {
            return false;
        } else if (strtoupper($password) === $password) {
            return false;
        }
        return true;
    }

    /* check if hasNumber */
    public function hasNumber(string $password): bool
    {
        for ($i = 0; $i < 10; $i++) {
            if (strchr($password, strval($i))) {
                return true;
            }
        }
        return false;
    }

    /* check if hasLetter <- CHECK LINE 10 */

    /* check if hasSpecialCharacter */
    public function hasSpecialCharacter(string $password): bool
    {
        if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $password))
        {
            return true;
        }
        return false;
    }

    /* check if contains firstName/lastName or reverse of firstName/lastName */
    public function checkIfContainsItOrReverseOfIt(string $password, string $firstName, string $lastName): bool
    {
        if(str_contains($password, $firstName) || str_contains($password, strrev($firstName)) ||
            str_contains($password, $lastName) || str_contains($password, strrev($lastName))) {
            return false;
        }
        return true;
    }
}