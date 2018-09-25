<?php

namespace Ovesco\FacturationBundle\Util;

use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Mapping\BaseMembre;
use Ovesco\FacturationBundle\Subscriber\DoctrineDebiteurSubscriber;

trait DebiteurTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="debiteur_id", type="string")
     */
    protected $debiteurId;

    /**
     * @var BaseMembre|BaseFamille
     */
    private $debiteur;

    /**
     * @return BaseFamille|BaseMembre
     */
    public function getDebiteur()
    {
        return $this->debiteur;
    }

    /**
     * @param BaseFamille|BaseMembre $debiteur
     */
    public function setDebiteur($debiteur)
    {
        $this->debiteur     = $debiteur;
        $this->debiteurId   = DoctrineDebiteurSubscriber::createId($debiteur);
    }
}