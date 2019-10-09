<?php

namespace TenteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;

/**
 * TenteModel
 *
 * @ORM\Table(name="tente_tente_models")
 * @ORM\Entity
 */
class TenteModel
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="form", type="json")
     */
    private $form;

    /**
     * @var DrawingPart[]
     *
     * @ORM\OneToMany(targetEntity="DrawingPart", mappedBy="tenteModel", cascade={"PERSIST", "REMOVE"})
     */
    private $drawingParts;

    /**
     * @var Tente[]
     *
     * @ORM\OneToMany(targetEntity="Tente", mappedBy="model")
     */
    private $tentes;

    /**
     * @var string[]
     *
     * @ORM\Column(type="array", name="parties")
     */
    private $parties;

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
     * @return TenteModel
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
     * Constructor
     */
    public function __construct()
    {
        $this->drawingParts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tentes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set form.
     *
     * @param string $form
     *
     * @return TenteModel
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form.
     *
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    public function getParsedForm() {
        return json_decode($this->form, true);
    }

    /**
     * Add drawingPart.
     *
     * @param \TenteBundle\Entity\DrawingPart $drawingPart
     *
     * @return TenteModel
     */
    public function addDrawingPart(\TenteBundle\Entity\DrawingPart $drawingPart)
    {
        $this->drawingParts[] = $drawingPart;
        $drawingPart->setTenteModel($this);
        return $this;
    }

    /**
     * Remove drawingPart.
     *
     * @param \TenteBundle\Entity\DrawingPart $drawingPart
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDrawingPart(\TenteBundle\Entity\DrawingPart $drawingPart)
    {
        return $this->drawingParts->removeElement($drawingPart);
    }

    /**
     * Get drawingParts.
     *
     * @return DrawingPart[]
     */
    public function getDrawingParts()
    {
        return $this->drawingParts;
    }

    public function removeDrawingParts() {
        $this->drawingParts->clear();
    }

    /**
     * Add tente.
     *
     * @param \TenteBundle\Entity\Tente $tente
     *
     * @return TenteModel
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

    /**
     * @return string[]
     */
    public function getParties()
    {
        return $this->parties;
    }

    /**
     * @param string[] $parties
     */
    public function setParties($parties)
    {
        $this->parties = $parties;
    }
}
