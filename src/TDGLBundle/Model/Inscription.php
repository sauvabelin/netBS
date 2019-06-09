<?php

namespace TDGLBundle\Model;

use NetBS\FichierBundle\Entity\Adresse;
use NetBS\FichierBundle\Entity\Attribution;
use NetBS\FichierBundle\Entity\ContactInformation;
use NetBS\FichierBundle\Entity\Email;
use NetBS\FichierBundle\Entity\Fonction;
use NetBS\FichierBundle\Entity\Groupe;
use NetBS\FichierBundle\Entity\Telephone;
use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Mapping\BaseMembre;
use Symfony\Component\Validator\Constraints as Assert;
use TDGLBundle\Entity\TDGLFamille;
use TDGLBundle\Entity\TDGLMembre;

class Inscription
{
    /**
     * @var int
     */
    public $familleId;

    /**
     * @var TDGLFamille
     */
    public $famille;

    /**
     * @Assert\NotBlank
     */
    public $prenom;

    /**
     * @Assert\NotBlank
     */
    public $sexe;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $nom;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\DateTime
     */
    public $naissance;

    /**
     * @var \DateTime
     */
    public $inscription;

    /**
     * @Assert\NotBlank
     */
    public $adresse;

    /**
     * @Assert\NotBlank
     */
    public $npa;

    /**
     * @Assert\NotBlank
     */
    public $localite;

    /**
     * @Assert\NotBlank
     */
    public $telephone;

    /**
     * @Assert\NotBlank
     */
    public $email;

    /**
     * @var string
     */
    public $professionsParents;

    /**
     * @Assert\NotNull
     * @var Groupe
     */
    public $unite;

    /**
     * @Assert\NotNull
     * @var Fonction
     */
    public $fonction;

    public function __construct()
    {
        $this->inscription = new \DateTime();
    }

    public function generateFamille() {

        if ($this->famille)
            return $this->famille;

        $adresse = new Adresse();
        $adresse->setRue($this->adresse)
            ->setNpa($this->npa)
            ->setLocalite($this->localite)
            ->setPays('CH');

        $famille = new TDGLFamille();
        $famille->setValidity(BaseFamille::VALIDE);
        $famille->setContactInformation(new ContactInformation());
        $famille->setNom($this->nom);
        $famille->addAdresse($adresse);
        $famille->addTelephone(new Telephone($this->telephone));
        $famille->addEmail(new Email($this->email));
        $famille->setProfessionsParents($this->professionsParents);
        $this->famille = $famille;
        return $famille;
    }

    public function generateMembre() {
        $membre = new TDGLMembre();
        $membre->setContactInformation(new ContactInformation());
        $membre->setStatut(BaseMembre::INSCRIT)
            ->setInscription($this->inscription)
            ->setNaissance($this->naissance)
            ->setPrenom($this->prenom)
            ->setSexe($this->sexe);

        $attribution = new Attribution();
        $attribution->setFonction($this->fonction)
            ->setGroupe($this->unite);
        $membre->addAttribution($attribution);
        return $membre;
    }
}
