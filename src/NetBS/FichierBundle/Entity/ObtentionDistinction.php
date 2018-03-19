<?php

namespace NetBS\FichierBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NetBS\FichierBundle\Mapping\BaseObtentionDistinction;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * ObtentionDistinction
 * @ORM\Table(name="netbs_fichier_obtentions_distinction")
 * @ORM\Entity()
 */
class ObtentionDistinction extends BaseObtentionDistinction
{
}

