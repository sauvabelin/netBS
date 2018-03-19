<?php

namespace SauvabelinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SauvabelinBundle\Model\MailingList;

/**
 * @package SauvabelinBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sauvabelin_netbs_rule_mailing_list")
 */
class RuleMailingList implements MailingList
{
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
     * @ORM\Column(name="from_adresse", type="string", length=255)
     */
    protected $fromAdresse;

    /**
     * @var string
     * @ORM\Column(name="expression_language_rule", type="text")
     */
    protected $elRule;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

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
    public function getElRule()
    {
        return $this->elRule;
    }

    /**
     * @param string $elRule
     */
    public function setElRule($elRule)
    {
        $this->elRule = $elRule;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFromAdresse()
    {
        return $this->fromAdresse;
    }

    /**
     * @param string $fromAdresse
     */
    public function setFromAdresse($fromAdresse)
    {
        $this->fromAdresse = $fromAdresse;
    }
}