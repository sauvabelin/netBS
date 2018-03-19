<?php

namespace SauvabelinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SauvabelinBundle\Model\MailingList;

/**
 * @package SauvabelinBundle\Entity
 * @ORM\Table(name="sauvabelin_netbs_redirect_mailing_list")
 * @ORM\Entity()
 */
class RedirectMailingList implements MailingList
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
     * @ORM\Column(name="to_adresses", type="text")
     */
    protected $toAdresses;

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

    /**
     * @return string
     */
    public function getToAdresses()
    {
        return $this->toAdresses;
    }

    public function getToAdressesAsArray() {

        return explode(",", $this->toAdresses);
    }

    /**
     * @param string $toAdresses
     */
    public function setToAdresses($toAdresses)
    {
        if(is_string($toAdresses))
            $toAdresses = str_replace(' ', '', str_replace("\n", '', str_replace("\n", '', $toAdresses)));

        elseif(is_array($toAdresses))
            $toAdresses = implode(',', $toAdresses);

        $this->toAdresses = $toAdresses;
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
}