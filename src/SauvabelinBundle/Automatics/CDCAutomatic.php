<?php

namespace SauvabelinBundle\Automatics;

use NetBS\CoreBundle\Form\Type\SwitchType;
use NetBS\CoreBundle\Model\BaseAutomatic;
use NetBS\CoreBundle\Model\ConfigurableAutomaticInterface;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\ParamTrait;
use NetBS\CoreBundle\Utils\Traits\SessionTrait;
use NetBS\FichierBundle\Exporter\PDFEtiquettesV2;
use NetBS\FichierBundle\Mapping\BaseAdresse;
use NetBS\FichierBundle\Mapping\BaseAttribution;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Model\AdressableInterface;
use NetBS\FichierBundle\Utils\Traits\FichierConfigTrait;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use NetBS\SecureBundle\Mapping\BaseUser;
use Symfony\Component\Form\FormBuilderInterface;

class CDCAutomatic extends BaseAutomatic implements ConfigurableAutomaticInterface
{
    use FichierConfigTrait, EntityManagerTrait, ParamTrait, SessionTrait;

    /**
     * @return string
     * Returns this list's name, displayed
     */
    public function getName()
    {
        return "Coeurs de chêne";
    }

    public function userAuthorization(BaseUser $user)
    {
        return $user->hasRole('ROLE_SG');
    }

    /**
     * @return string
     * Returns this list's description, displayed
     */
    public function getDescription()
    {
        return "Toutes les familles et membres censés reçevoir le coeur de chêne";
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getItems($data = null)
    {
        $membres = $this->entityManager->getRepository($this->fichierConfig->getMembreClass())->findBy(['statut' => BaseMembre::INSCRIT]);
        $adabs = array_map(function(BaseAttribution $attribution) {
            return $attribution->getMembre();
        }, $this->entityManager->getRepository($this->fichierConfig->getGroupeClass())->find($this->parameterManager->getValue('bs', 'groupe.adabs_id'))->getActivesAttributions());


        $items = array_unique(array_merge($membres, $adabs));

        if ($data['merge'] === true)
            $items = PDFEtiquettesV2::merge($items);

        $adressables = array_filter($items, function(AdressableInterface $adressable) {
            return $adressable->getSendableAdresse() instanceof BaseAdresse;
        });

        $amountItems = count($items);
        $amountAdressables = count($adressables);

        if ($amountAdressables !== $amountItems) {
            $this->session->getFlashBag()->add('warning', $amountItems - $amountAdressables . " n'ont pas d'adresses !");
        }

        return $adressables;
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return "sauvabelin.cdc";
    }

    /**
     * @param FormBuilderInterface $builder
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder->add('merge', SwitchType::class, ['label' => 'Fusionner les familles']);
    }

    /**
     * Returns something that will be injected in the form
     * builder, and available in your automatic
     * @return mixed
     */
    public function buildDataHolder()
    {
        return ['merge' => true];
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return AdressableInterface::class;
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn('Nom', function($item) {
                return $item instanceof BaseMembre
                    ? $item->getFullName()
                    : $item->__toString();
            }, SimpleColumn::class)
            ->addColumn('Rue', 'sendableAdresse.rue', SimpleColumn::class)
            ->addColumn('NPA', 'sendableAdresse.npa', SimpleColumn::class)
            ->addColumn('Localité', 'sendableAdresse.localite', SimpleColumn::class)
        ;
    }
}