<?php

namespace StreetWarBundle\Model;

class Participant
{
    public $user;

    public $cible;

    public $isDead;

    public $kills;

    public function __construct(array $data)
    {
        $this->user = $data[0];
        $this->kills = count($data) - 2;

        $last = $data[count($data) - 1];
        $this->isDead = $last === 'dead';
        $this->cible = $this->isDead ? null : $last;
    }
}
