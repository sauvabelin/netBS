<?php

namespace Ovesco\HikeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use NetBS\SecureBundle\Mapping\BaseUser;

/**
 * Hike
 *
 * @ORM\Table(name="hike")
 * @ORM\Entity(repositoryClass="Ovesco\HikeBundle\Repository\HikeRepository")
 */
class Hike
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
     * @var JourDeMarche[]
     * @ORM\OneToMany(targetEntity="JourDeMarche", mappedBy="hike")
     */
    private $joursDeMarche;

    /**
     * @var BaseUser
     */
    private $auteur;

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
     * @return BaseUser
     */
    public function getAuteur()
    {
        return $this->auteur;
    }

    /**
     * @param BaseUser $auteur
     */
    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->joursDeMarche = new ArrayCollection();
    }

    /**
     * Add joursDeMarche.
     *
     * @param \Ovesco\HikeBundle\Entity\JourDeMarche $joursDeMarche
     *
     * @return Hike
     */
    public function addJoursDeMarche(JourDeMarche $joursDeMarche)
    {
        $this->joursDeMarche[] = $joursDeMarche;

        return $this;
    }

    /**
     * Remove joursDeMarche.
     *
     * @param \Ovesco\HikeBundle\Entity\JourDeMarche $joursDeMarche
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeJoursDeMarche(JourDeMarche $joursDeMarche)
    {
        return $this->joursDeMarche->removeElement($joursDeMarche);
    }

    /**
     * Get joursDeMarche.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJoursDeMarche()
    {
        return $this->joursDeMarche;
    }
}
