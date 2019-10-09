<?php

namespace TenteBundle\Model;

class ReparationPartie
{
    public $nom;

    public $sent = false;

    public function __construct($nom)
    {
        $this->nom = $nom;
    }
}