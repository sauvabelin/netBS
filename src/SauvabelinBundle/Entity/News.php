<?php

namespace SauvabelinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity()
 */
class News
{
    use TimestampableEntity;

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
     *
     * @ORM\Column(name="titre", type="string", length=255)
     */
    protected $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    protected $contenu;

    /**
     * @var bool
     *
     * @ORM\Column(name="importante", type="boolean")
     */
    protected $importante;

    /**
     * @var BSUser
     *
     * @ORM\ManyToOne(targetEntity="SauvabelinBundle\Entity\BSUser")
     */
    protected $user;

    /**
     * @var NewsChannel
     *
     * @ORM\ManyToOne(targetEntity="SauvabelinBundle\Entity\NewsChannel", inversedBy="news")
     */
    protected $channel;

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
     * Set titre.
     *
     * @param string $titre
     *
     * @return News
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre.
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu.
     *
     * @param string $contenu
     *
     * @return News
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu.
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set user.
     *
     * @param \SauvabelinBundle\Entity\BSUser|null $user
     *
     * @return News
     */
    public function setUser(BSUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \SauvabelinBundle\Entity\BSUser|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set channel.
     *
     * @param \SauvabelinBundle\Entity\NewsChannel|null $channel
     *
     * @return News
     */
    public function setChannel(NewsChannel $channel = null)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel.
     *
     * @return \SauvabelinBundle\Entity\NewsChannel|null
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return bool
     */
    public function isImportante()
    {
        return $this->importante;
    }

    /**
     * @param bool $importante
     * @return News
     */
    public function setImportante($importante)
    {
        $this->importante = $importante;
        return $this;
    }
}
