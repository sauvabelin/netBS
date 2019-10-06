<?php

namespace TenteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;

/**
 * DrawingPart
 *
 * @ORM\Table(name="tente_drawing_parts")
 * @ORM\Entity
 */
class DrawingPart
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
     * @var resource
     *
     * @ORM\Column(name="image_path", type="string", length=255)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var TenteModel
     *
     * @ORM\ManyToOne(targetEntity="TenteModel", inversedBy="drawingParts")
     */
    private $tenteModel;

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
     * Set image.
     *
     * @param string $image
     *
     * @return DrawingPart
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return DrawingPart
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set tenteModel.
     *
     * @param \TenteBundle\Entity\TenteModel|null $tenteModel
     *
     * @return DrawingPart
     */
    public function setTenteModel(\TenteBundle\Entity\TenteModel $tenteModel = null)
    {
        $this->tenteModel = $tenteModel;

        return $this;
    }

    /**
     * Get tenteModel.
     *
     * @return \TenteBundle\Entity\TenteModel|null
     */
    public function getTenteModel()
    {
        return $this->tenteModel;
    }
}
