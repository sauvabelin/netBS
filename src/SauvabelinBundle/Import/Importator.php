<?php

namespace SauvabelinBundle\Import;

use SauvabelinBundle\Import\Model\WNGAttribution;
use SauvabelinBundle\Import\Model\WNGDistinction;
use SauvabelinBundle\Import\Model\WNGFonction;
use SauvabelinBundle\Import\Model\WNGMembre;
use SauvabelinBundle\Import\Model\WNGObtentionDistinction;
use SauvabelinBundle\Import\Model\WNGUnite;

class Importator
{
    private $pdo;

    public function __construct($host, $name, $user, $pass)
    {
        $this->pdo = new \PDO("mysql:dbname=sauvabelin_fich;host=$host", $user, $pass);
    }

    /**
     * @return WNGMembre[]
     */
    public function loadMembres() {

        $membres    = [];

        foreach($this->request("SELECT * FROM membres") as $membre)
            $membres[$membre['id_membre']] = new WNGMembre($membre);

        return $membres;
    }

    /**
     * @return WNGAttribution[]
     */
    public function loadAttributions() {

        $data   = [];

        foreach($this->request("SELECT * FROM membres_attributions") as $item)
            $data[] = new WNGAttribution($item);

        return $data;
    }

    /**
     * @return WNGDistinction[]
     */
    public function loadDistinctions() {

        $data   = [];

        foreach($this->request("SELECT * FROM distinctions") as $item)
            $data[$item['id_distinction']] = new WNGDistinction($item);

        return $data;
    }

    /**
     * @return WNGObtentionDistinction[]
     */
    public function loadObtentionsDistinctions() {

        $data   = [];

        foreach($this->request("SELECT * FROM membres_distinctions") as $item)
            $data[] = new WNGObtentionDistinction($item);

        return $data;
    }

    /**
     * @return WNGFonction[]
     */
    public function loadFonctions() {

        $data   = [];

        foreach($this->request("SELECT * FROM attributions") as $item)
            $data[$item['id_attribution']] = new WNGFonction($item);

        return $data;
    }

    /**
     * @return WNGUnite[]
     */
    public function loadGroupes() {

        $data   = [];

        foreach($this->request("SELECT * FROM unites") as $item)
            $data[$item['id_unite']] = new WNGUnite($item);

        return $data;
    }

    private function request($request) {

        $query  = $this->pdo->prepare($request);
        $query->execute();

        return $query->fetchAll();
    }
}
