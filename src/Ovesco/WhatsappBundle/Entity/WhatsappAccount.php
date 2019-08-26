<?php

namespace Ovesco\WhatsappBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\SecureBundle\Mapping\BaseUser;

/**
 * Class WhatsappAccount
 * @package Ovesco\WhatsappBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="ovesco_whatsapp_accounts")
 */
class WhatsappAccount
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
     * @ORM\Column(type="string", length=255, name="account_id")
     */
    protected $accountId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, name="from_number")
     */
    protected $from;

    /**
     * @var InboundMessage[]
     *
     * @ORM\OneToMany(targetEntity="InboundMessage", mappedBy="account")
     */
    protected $messages;

    /**
     * @var BaseUser
     */
    protected $user;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
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
     * Set accountId.
     *
     * @param string $accountId
     *
     * @return WhatsappAccount
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId.
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set from.
     *
     * @param string $from
     *
     * @return WhatsappAccount
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Add message.
     *
     * @param InboundMessage $message
     *
     * @return WhatsappAccount
     */
    public function addMessage(InboundMessage $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message.
     *
     * @param InboundMessage $message
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMessage(InboundMessage $message)
    {
        return $this->messages->removeElement($message);
    }

    /**
     * Get messages.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return BaseUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param BaseUser $user
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
