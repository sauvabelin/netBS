<?php

namespace NetBS\FichierBundle\Utils;

class FichierHelper
{
    /**
     * @param $class
     * @param bool $flip
     * @return array
     */
    public static function getStatutChoices($class, $flip = false) {

        $choices = call_user_func([$class, 'getStatutChoices']);
        return $flip ? array_flip($choices) : $choices;
    }

    /**
     * @param $class
     * @param bool $flip
     * @return array
     */
    public static function getValidityChoices($class, $flip = false) {

        $choices = call_user_func([$class, 'getValidityChoices']);
        return $flip ? array_flip($choices) : $choices;
    }
}