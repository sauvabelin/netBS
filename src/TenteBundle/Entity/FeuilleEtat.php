<?php

namespace TenteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use NetBS\FichierBundle\Utils\Entity\RemarqueTrait;
use Sabre\VObject\Property\Boolean;
use SauvabelinBundle\Entity\BSGroupe;
use SauvabelinBundle\Entity\BSUser;

/**
 * FeuilleEtat
 *
 * @ORM\Table(name="tente_feuilles_etat")
 * @ORM\Entity
 */
class FeuilleEtat
{
    use TimestampableEntity, RemarqueTrait;

    const STATUS_OK = 'status_ok';
    const STATUS_NO_OK = 'status_no_ok';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Tente
     *
     * @ORM\ManyToOne(targetEntity="Tente", inversedBy="feuillesEtat")
     */
    private $tente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut", type="date")
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="date")
     */
    private $fin;

    /**
     * @var Boolean
     *
     * @ORM\Column(name="validated", type="boolean")
     */
    private $validated = false;

    /**
     * @var string
     *
     * @ORM\Column(name="activity", length=255, type="string")
     */
    private $activity;

    /**
     * @var BSUser
     *
     * @ORM\ManyToOne(targetEntity="SauvabelinBundle\Entity\BSUser")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=255)
     */
    private $statut = self::STATUS_OK;

    /**
     * @var BSGroupe
     *
     * @ORM\ManyToOne(targetEntity="SauvabelinBundle\Entity\BSGroupe")
     */
    private $groupe;

    /**
     * @var string
     *
     * @ORM\Column(name="form_data", type="text")
     */
    private $formData;

    /**
     * @var string
     *
     * @ORM\Column(name="drawing_data", type="text")
     */
    private $drawingData;

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
     * Constructor
     */
    public function __construct()
    {
        $this->debut = new \DateTime();
        $this->fin = new \DateTime();
    }

    public static function getStatutChoices() {
        return [
            self::STATUS_NO_OK => 'Il y a des problèmes',
            self::STATUS_OK => 'Tout est en état'
        ];
    }

    /**
     * Set formData.
     *
     * @param string $formData
     *
     * @return FeuilleEtat
     */
    public function setFormData($formData)
    {
        $this->formData = $formData;

        return $this;
    }

    /**
     * Get formData.
     *
     * @return string
     */
    public function getFormData()
    {
        return $this->formData;
    }

    public function getFormDataParsed() {

        return json_decode($this->formData, true);
    }

    /**
     * Set tente.
     *
     * @param \TenteBundle\Entity\Tente|null $tente
     *
     * @return FeuilleEtat
     */
    public function setTente(\TenteBundle\Entity\Tente $tente = null)
    {
        $this->tente = $tente;

        return $this;
    }

    /**
     * Get tente.
     *
     * @return \TenteBundle\Entity\Tente|null
     */
    public function getTente()
    {
        return $this->tente;
    }

    /**
     * Set user.
     *
     * @param \SauvabelinBundle\Entity\BSUser|null $user
     *
     * @return FeuilleEtat
     */
    public function setUser(\SauvabelinBundle\Entity\BSUser $user = null)
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
     * Set groupe.
     *
     * @param \SauvabelinBundle\Entity\BSGroupe|null $groupe
     *
     * @return FeuilleEtat
     */
    public function setGroupe(\SauvabelinBundle\Entity\BSGroupe $groupe = null)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe.
     *
     * @return \SauvabelinBundle\Entity\BSGroupe|null
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param string $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return string
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param string $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return Boolean
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * @param Boolean $validated
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
    }

    /**
     * @return \DateTime
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * @param \DateTime $debut
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;
    }

    /**
     * @return \DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * @param \DateTime $fin
     */
    public function setFin($fin)
    {
        $this->fin = $fin;
    }

    /**
     * @return string
     */
    public function getDrawingData()
    {
        return $this->drawingData;
    }

    public function getDrawingDataParsed() {

        return json_decode($this->drawingData, true);
    }

    /**
     * @param string $drawingData
     */
    public function setDrawingData($drawingData)
    {
        $this->drawingData = $drawingData;
    }
}
