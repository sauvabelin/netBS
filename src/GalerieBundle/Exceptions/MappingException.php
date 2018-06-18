<?php

namespace GalerieBundle\Exceptions;

class MappingException extends \Exception
{
    private $level;
    private $user;

    public function __construct($user, $level, $message)
    {
        parent::__construct($message, 0, null);

        $this->level    = $level;
        $this->user     = $user;
    }

    public function getUser() {
        return $this->user;
    }

    public function getLevel() {
        return $this->level;
    }
}