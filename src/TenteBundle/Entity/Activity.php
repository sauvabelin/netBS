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
     * @ORM\Column(name="name_activity", type="string", length=255)
     */
    private $name;

    /**
     * @var Tente[]
     *
     * @ORM\ManyToMany(targetEntity="TenteBundle\Entity\Tente", inversedBy="activities")
     */
    private $tentes;
}
