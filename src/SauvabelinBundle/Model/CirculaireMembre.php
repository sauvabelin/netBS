<?php

namespace SauvabelinBundle\Model;

use NetBS\FichierBundle\Entity\Adresse;
use NetBS\FichierBundle\Entity\Attribution;
use NetBS\FichierBundle\Entity\Email;
use NetBS\FichierBundle\Entity\Famille;
use NetBS\FichierBundle\Entity\Fonction;
use NetBS\FichierBundle\Entity\Geniteur;
use NetBS\FichierBundle\Entity\Groupe;
use NetBS\FichierBundle\Entity\Telephone;
use NetBS\FichierBundle\Mapping\Personne;
use SauvabelinBundle\Entity\BSMembre;
use SauvabelinBundle\Entity\Membre;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * @Assert\GroupSequenceProvider()
 */
class CirculaireMembre implements GroupSequenceProviderInterface
{
    /**
     * @var int
     */
    public $familleId;

    /**
     * @var string
     * @Assert\NotBlank(groups={"default"})
     */
    public $prenom;

    /**
     * @var int
     * @Assert\NotBlank(groups={"default"})
     */
    public $numero;

    /**
     * @var string
     * @Assert\NotBlank(groups={"default"})
     */
    public $nom;

    /**
     * @var string
     * @Assert\Choice({"homme", "femme"}, groups={"default"})
     */
    public $sexe;

    /**
     * @var \DateTime
     * @Assert\DateTime(groups={"default"})
     * @Assert\NotBlank(groups={"default"})
     */
    public $naissance;

    /**
     * @var string
     * @Assert\NotBlank(groups={"noFamily"})
     */
    public $adresse;

    /**
     * @var int
     * @Assert\NotBlank(groups={"noFamily"})
     * @Assert\Range(min=1000, max=99999, groups={"noFamily"})
     */
    public $npa;

    /**
     * @var string
     * @Assert\NotBlank(groups={"noFamily"})
     */
    public $localite;

    /**
     * @Assert\Email(groups={"noFamily"})
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $telephone;

    /**
     * @var string
     */
    public $natel;

    /**
     * @Assert\NotNull(groups={"default"})
     * @var Fonction
     */
    public $fonction;

    /**
     * @Assert\NotNull(groups={"default"})
     * @var Groupe
     */
    public $groupe;

    /**
     * @var string
     * @Assert\NotBlank(groups={"geniteur1"})
     */
    public $r1statut = Geniteur::MERE;

    /**
     * @var string
     * @Assert\NotBlank(groups={"geniteur1"})
     */
    public $r1sexe  = Personne::FEMME;

    /**
     * @var string
     */
    public $r1nom;

    /**
     * @var string
     * @Assert\NotBlank(groups={"geniteur1"})
     */
    public $r1prenom;

    /**
     * @var string
     */
    public $r1adresse;

    /**
     * @var string
     */
    public $r1npa;

    /**
     * @var string
     */
    public $r1localite;

    /**
     * @var string
     */
    public $r1telephone;

    /**
     * @Assert\Email(groups={"noFamily"})
     * @var string
     */
    public $r1email;

    /**
     * @var string
     */
    public $r1profession;

    /**
     * @var string
     * @Assert\NotBlank(groups={"geniteur2"})
     */
    public $r2statut = Geniteur::PERE;

    /**
     * @var string
     * @Assert\NotBlank(groups={"geniteur2"})
     */
    public $r2sexe = Personne::HOMME;

    /**
     * @var string
     */
    public $r2nom;

    /**
     * @var string
     * @Assert\NotBlank(groups={"geniteur2"})
     */
    public $r2prenom;

    /**
     * @var string
     */
    public $r2adresse;

    /**
     * @var string
     */
    public $r2npa;

    /**
     * @var string
     */
    public $r2localite;

    /**
     * @var string
     */
    public $r2telephone;

    /**
     * @var string
     */
    public $r2email;

    /**
     * @var string
     */
    public $r2profession;

    protected $famille = null;

    public function setFamille(Famille $famille) {

        $this->famille  = $famille;
    }

    public function getGroupSequence()
    {
        return [
            'default',
            $this->famille ? '' : 'noFamily',
            $this->r1prenom ? 'geniteur1' : '',
            $this->r2prenom ? 'geniteur2' : ''
        ];
    }

    public function generateFamille() {

        if($this->famille)
            return $this->famille;

        $famille    = new Famille();
        $famille->setNom($this->nom);
        $adresse    = new Adresse();
        $adresse->setRue($this->adresse)
            ->setNpa($this->npa)
            ->setLocalite($this->localite);

        $famille->setAdresse($adresse);

        if($this->telephone)
            $famille->addTelephone(new Telephone($this->telephone));

        if($this->email)
            $famille->addEmail(new Email($this->email));

        if($this->r1prenom) {
            $geniteur   = new Geniteur();
            $geniteur->setProfession($this->r1profession)
                ->setSexe($this->r1sexe)
                ->setPrenom($this->r1prenom)
                ->setStatut($this->r1statut)
                ->setNom($this->r1nom);

            $geniteur->setAdresse((new Adresse())->setRue($this->r1adresse)
                    ->setNpa($this->r1npa)
                    ->setLocalite($this->r1localite));

            if($this->r1telephone)
                $geniteur->addTelephone(new Telephone($this->r1telephone));
            if($this->r1email)
                $geniteur->addEmail(new Email($this->r1email));

            $famille->addGeniteur($geniteur);
        }

        if($this->r2prenom) {
            $geniteur   = new Geniteur();
            $geniteur->setProfession($this->r2profession)
                ->setSexe($this->r2sexe)
                ->setPrenom($this->r2prenom)
                ->setStatut($this->r2statut)
                ->setNom($this->r2nom);

            $geniteur->setAdresse((new Adresse())->setRue($this->r2adresse)
                ->setNpa($this->r2npa)
                ->setLocalite($this->r2localite));

            if($this->r2telephone)
                $geniteur->addTelephone(new Telephone($this->r2telephone));
            if($this->r2email)
                $geniteur->addEmail(new Email($this->r2email));

            $famille->addGeniteur($geniteur);
        }

        return $famille;
    }

    public function getMembre() {

        $membre = new BSMembre();
        $membre
            ->setNumero($this->numero)
            ->setNaissance($this->naissance)
            ->setPrenom($this->prenom)
            ->setSexe($this->sexe);

        if($this->natel)
            $membre->addTelephone(new Telephone($this->natel));

        $attribution    = new Attribution();
        $attribution->setFonction($this->fonction)
            ->setGroupe($this->groupe);

        $membre->addAttribution($attribution);
        return $membre;
    }
}