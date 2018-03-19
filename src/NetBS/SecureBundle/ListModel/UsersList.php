<?php

namespace NetBS\SecureBundle\ListModel;

use NetBS\CoreBundle\Form\Type\SwitchType;
use NetBS\CoreBundle\ListModel\Action\LinkAction;
use NetBS\CoreBundle\ListModel\Action\RemoveAction;
use NetBS\CoreBundle\ListModel\Column\ActionColumn;
use NetBS\CoreBundle\ListModel\Column\ArrayColumn;
use NetBS\CoreBundle\ListModel\Column\HelperColumn;
use NetBS\CoreBundle\ListModel\Column\XEditableColumn;
use NetBS\FichierBundle\Utils\Traits\SecureConfigTrait;
use NetBS\ListBundle\Column\SimpleColumn;
use NetBS\ListBundle\Model\BaseListModel;
use NetBS\ListBundle\Model\ListColumnsConfiguration;
use NetBS\CoreBundle\Utils\Traits\EntityManagerTrait;
use NetBS\CoreBundle\Utils\Traits\RouterTrait;
use NetBS\SecureBundle\Entity\Role;
use NetBS\SecureBundle\Mapping\BaseUser;

class UsersList extends BaseListModel
{
    use EntityManagerTrait, RouterTrait, SecureConfigTrait;

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->entityManager->getRepository($this->getManagedItemsClass())->findAll();
    }

    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass()
    {
        return $this->getSecureConfig()->getUserClass();
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return 'netbs.secure.users';
    }

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration)
    {
        $configuration
            ->addColumn("Nom d'utilisateur", function(BaseUser $user) {

                $html   = $user->getUsername();
                if(!$user->getIsActive())
                    $html .= " <span class='label label-danger'>Désactivé</span>";
                return $html;
            }, SimpleColumn::class)
            ->addColumn("ID BDD", 'id', SimpleColumn::class)
            ->addColumn("E-mail", 'email', SimpleColumn::class)
            ->addColumn("Membre lié", 'membre', HelperColumn::class)
            ->addColumn("Compte activé", null, XEditableColumn::class, array(
                XEditableColumn::PROPERTY   => 'isActive',
                XEditableColumn::TYPE_CLASS => SwitchType::class,
            ))
            ->addColumn('Autorisations', 'roles', ArrayColumn::class, array(
                ArrayColumn::FORMATTING => function(Role $role) {
                    return $role->getRole() . " - " . $role->getDescription();
                }
            ))
            ->addColumn("Actions", null,ActionColumn::class, array(
                ActionColumn::ACTIONS_KEY   => [
                    new LinkAction(function(BaseUser $user) {
                        return $this->router->generate('netbs.secure.user.edit_user', array('id' => $user->getId()));
                    }),
                    new RemoveAction($this->router)
                ]
            ))
        ;
    }
}