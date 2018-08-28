<?php

namespace SauvabelinBundle\Controller;

use NetBS\CoreBundle\Utils\Modal;
use SauvabelinBundle\Entity\BSUser;
use SauvabelinBundle\Form\AdminChangePasswordType;
use SauvabelinBundle\Model\AdminChangePassword;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package SauvabelinBundle\Controller
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/latest-accounts", name="sauvabelin.user.latest_created")
     */
    public function latestCreatedAction() {

        return $this->render('@Sauvabelin/user/last_created_accounts.html.twig');
    }

    /**
     * @Route("/user/admin-change-password/{id}", name="sauvabelin.user.admin_change_password_modal")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function modalAdminChangePasswordAction(Request $request, $id) {

        $manager    = $this->get('netbs.secure.user_manager');
        $encoder    = $this->get('security.password_encoder');

        /** @var BSUser $user */
        $user       = $manager->find($id);
        $form       = $this->createForm(AdminChangePasswordType::class, new AdminChangePassword());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            /** @var AdminChangePassword $data */
            $data   = $form->getData();

            if($data->isForceChange())
                $user->setNewPasswordRequired(true);

            $user->setPassword($encoder->encodePassword($user, $data->getPassword()));
            $manager->updateUser($user);
        }

        return $this->render('@Sauvabelin/user/change_password.modal.twig', [
            'title' => 'Changer un mot de passe',
            'form'  => $form->createView()
        ], Modal::renderModal($form));
    }
}
