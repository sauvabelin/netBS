<?php

namespace Ovesco\FacturationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="ovesco_facturation_comptes")
 * @ORM\Entity
 */
class Compte
{
    use TimestampableEntity, RemarqueTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="ccp", type="string", length=255, nullable=false, unique=true)
     * @Groups({"default"})
     */
    protected $ccp;

    /**
     * @var string
     * @ORM\Column(name="iban", type="string", length=255, nullable=false, unique=true)
     */
    protected $iban;

    /**
     * @var string
     * @ORM\Column(name="addresse1", type="string", length=255, nullable=true, unique=false)
     * @Groups({"default"})
     */
    protected $line1;

    /**
     * @var string
     * @ORM\Column(name="addresse2", type="string", length=255, nullable=true, unique=false)
     * @Groups({"default"})
     */
    protected $line2;

    /**
     * @var string
     * @ORM\Column(name="addresse3", type="string", length=255, nullable=true, unique=false)
     * @Groups({"default"})
     */
    protected $line3;

    /**
     * @var string
     * @ORM\Column(name="addresse4", type="string", length=255, nullable=true, unique=false)
     * @Groups({"default"})
     */
    protected $line4;

    public function __toString()
    {
        return $this->ccp;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * @param string $line1
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;
    }

    /**
     * @return string
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * @param string $line2
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    /**
     * @return string
     */
    public function getLine3()
    {
        return $this->line3;
    }

    /**
     * @param string $line3
     */
    public function setLine3($line3)
    {
        $this->line3 = $line3;
    }

    /**
     * @return string
     */
    public function getLine4()
    {
        return $this->line4;
    }

    /**
     * @param string $line4
     */
    public function setLine4($line4)
    {
        $this->line4 = $line4;
    }

    /**
     * @return string
     */
    public function getCcp()
    {
        return $this->ccp;
    }

    /**
     * @param string $ccp
     */
    public function setCcp($ccp)
    {
        $this->ccp = $ccp;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    }
}
