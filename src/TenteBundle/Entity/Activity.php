<?php

namespace TenteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;

/**
 * @ORM\Table(name="tente_activities")
 * @ORM\Entity
 */
class Activity
{
    use TimestampableEntity, RemarqueTrait;

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Tente[]
     *
     * @ORM\ManyToMany(targetEntity="Tente", mappedBy="activities")
     */
    private $tentes;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tentes = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name.
     *
     * @param string $name
     *
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add tente.
     *
     * @param \TenteBundle\Entity\Tente $tente
     *
     * @return Activity
     */
    public function addTente(\TenteBundle\Entity\Tente $tente)
    {
        $this->tentes[] = $tente;

        return $this;
    }

    /**
     * Remove tente.
     *
     * @param \TenteBundle\Entity\Tente $tente
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTente(\TenteBundle\Entity\Tente $tente)
    {
        return $this->tentes->removeElement($tente);
    }

    /**
     * Get tentes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTentes()
    {
        return $this->tentes;
    }
}
