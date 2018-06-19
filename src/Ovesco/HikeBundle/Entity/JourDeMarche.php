<?php

namespace Ovesco\HikeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JourDeMarche
 *
 * @ORM\Table(name="jour_de_marche")
 * @ORM\Entity(repositoryClass="Ovesco\HikeBundle\Repository\JourDeMarcheRepository")
 */
class JourDeMarche
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="origine", type="string", length=255)
     */
    private $origine;

    /**
     * @var string
     *
     * @ORM\Column(name="destination", type="string", length=255)
     */
    private $destination;

    /**
     * @var string
     *
     * @ORM\Column(name="kml", type="text")
     */
    private $kml;

    /**
     * @var string
     *
     * @ORM\Column(name="profil", type="string", length=255)
     */
    private $profil;

    /**
     * @var Hike
     * @ORM\ManyToOne(targetEntity="Hike", inversedBy="joursDeMarche")
     */
    private $hike;

    /**
     * @var string
     * @ORM\Column(name="remarques", type="text")
     */
    private $remarques;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set origine.
     *
     * @param string $origine
     *
     * @return JourDeMarche
     */
    public function setOrigine($origine)
    {
        $this->origine = $origine;

        return $this;
    }

    /**
     * Get origine.
     *
     * @return string
     */
    public function getOrigine()
    {
        return $this->origine;
    }

    /**
     * Set destination.
     *
     * @param string $destination
     *
     * @return JourDeMarche
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Get destination.
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set kml.
     *
     * @param string $kml
     *
     * @return JourDeMarche
     */
    public function setKml($kml)
    {
        $this->kml = $kml;

        return $this;
    }

    /**
     * Get kml.
     *
     * @return string
     */
    public function getKml()
    {
        return $this->kml;
    }

    /**
     * Set profil.
     *
     * @param string $profil
     *
     * @return JourDeMarche
     */
    public function setProfil($profil)
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * Get profil.
     *
     * @return string
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * Set remarques.
     *
     * @param string $remarques
     *
     * @return JourDeMarche
     */
    public function setRemarques($remarques)
    {
        $this->remarques = $remarques;

        return $this;
    }

    /**
     * Get remarques.
     *
     * @return string
     */
    public function getRemarques()
    {
        return $this->remarques;
    }

    /**
     * Set hike.
     *
     * @param \Ovesco\HikeBundle\Entity\Hike|null $hike
     *
     * @return JourDeMarche
     */
    public function setHike(Hike $hike = null)
    {
        $this->hike = $hike;

        return $this;
    }

    /**
     * Get hike.
     *
     * @return \Ovesco\HikeBundle\Entity\Hike|null
     */
    public function getHike()
    {
        return $this->hike;
    }
}
