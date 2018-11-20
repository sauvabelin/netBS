<?php

namespace NetBS\SecureBundle\Controller;

use NetBS\SecureBundle\Event\UserPasswordChangeEvent;
use NetBS\SecureBundle\Form\ChangePasswordType;
use NetBS\SecureBundle\Form\UserType;
use NetBS\SecureBundle\Model\ChangePassword;
use NetBS\SecureBundle\Voter\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package NetBS\SecureBundle\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/user/list", name="netbs.secure.user.list_users")
     */
    public function listUsersAction(Request $request) {

        $username = empty($request->get('username')) ? null : $request->get('username');
        return $this->render('@NetBSSecure/user/list_users.html.twig', [
            'username' => $username
        ]);
    }

    /**
     * @Route("/user/edit/{id}", name="netbs.secure.user.edit_user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateUserAction(Request $request, $id) {

        $manager    = $this->get('netbs.secure.user_manager');
        $user       = $manager->find($id);
        $form       = $this->createForm(UserType::class, $user, ['operation' => CRUD::UPDATE]);

        $form->handleRequest($request);
        if($form->isValid() && $form->isSubmitted()) {

            $user   = $form->getData();

            $manager->updateUser($user);
            $this->addFlash("success", "{$user->getUsername()} mis à jour");
            return $this->redirectToRoute('netbs.secure.user.list_users');
        }

        return $this->render('@NetBSCore/generic/form.generic.twig', array(
            'header'    => "Modifier {$user->getUsername()}",
            'form'      => $form->createView()
        ));
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @Route("/user/delete/{id}", name="netbs.secure.user.delete_user")
     */
    public function deleteUserAction($id) {

        $em             = $this->get('doctrine.orm.entity_manager');
        $secureConfig   = $this->get('netbs.secure.config');
        $user           = $em->find($secureConfig->getUserClass(), $id);
        $manager        = $this->get('netbs.secure.user_manager');

        try {
            $manager->deleteUser($user);
        } catch (\ErrorException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->get('netbs.core.history')->getPreviousRoute();
    }

    /**
     * @Route("/user/add", name="netbs.secure.user.add_user")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addUserAction(Request $request) {

        $config     = $this->get('netbs.secure.config');
        $manager    = $this->get('netbs.secure.user_manager');
        $user       = $config->createUser();
        $form       = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $user       = $form->getData();
            $password   = $manager->encodePassword($user, $user->getPassword());

            $user->setPassword($password);
            $manager->createUser($user);

            $this->addFlash("success", "Utilisateur {$user->getUsername()} ajouté!");
            return $this->redirectToRoute("netbs.secure.user.list_users");
        }

        return $this->render('@NetBSCore/generic/form.generic.twig', array(
            'header'    => 'Nouvel utilisateur',
            'subHeader' => "Ajouter un utilisateur manuellement",
            'form'  => $form->createView()
        ));
    }

    /**
     * @Route("/user/my-account", name="netbs.secure.user.account_page")
     */
    public function accountPageAction(Request $request) {

        $manager            = $this->get('netbs.secure.user_manager');
        $user               = $this->getUser();
        $userForm           = $this->createForm(UserType::class, $user);

        $changePassword     = new ChangePassword();
        $changePasswordForm = $this->createForm(ChangePasswordType::class, $changePassword);

        $changePasswordForm->handleRequest($request);

        if($changePasswordForm->isValid() && $changePasswordForm->isSubmitted()) {

            $newPassword    = $changePassword->getNewPassword();
            $password       = $this->get('security.password_encoder')->encodePassword($user, $newPassword);

            $user->setPassword($password);
            $manager->updateUser($user);

            $this->get('event_dispatcher')->dispatch(UserPasswordChangeEvent::NAME, new UserPasswordChangeEvent($user, $newPassword));

            $this->addFlash("success", "Mot de passe changé avec succès!");
            return $this->redirectToRoute('netbs.secure.user.account_page');
        }

        return $this->render('@NetBSSecure/user/account_page.html.twig', array(
            'user'          => $user,
            'membre'        => $user->getMembre(),
            'userForm'      => $userForm->createView(),
            'cpForm'        => $changePasswordForm->createView(),
        ));
    }
}