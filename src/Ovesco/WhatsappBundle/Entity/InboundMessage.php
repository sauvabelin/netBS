<?php

namespace Ovesco\WhatsappBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * InboundMessage
 * @ORM\Entity
 * @ORM\Table(name="ovesco_whatsapp_messages")
 */
class InboundMessage
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
     * @ORM\Column(type="string", length=255, name="body")
     */
    private $body;

    /**
     * @var WhatsappAccount
     *
     * @ORM\ManyToOne(targetEntity="WhatsappAccount", inversedBy="messages")
     */
    protected $account;

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
     * Set body.
     *
     * @param string $body
     *
     * @return InboundMessage
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set account.
     *
     * @param \Ovesco\WhatsappBundle\Entity\WhatsappAccount|null $account
     *
     * @return InboundMessage
     */
    public function setAccount(WhatsappAccount $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account.
     *
     * @return \Ovesco\WhatsappBundle\Entity\WhatsappAccount|null
     */
    public function getAccount()
    {
        return $this->account;
    }
}
