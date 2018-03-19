<?php

namespace NetBS\SecureBundle\Service;

use Doctrine\ORM\EntityManager;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Exceptions\UserCreationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    /**
     * @var SecureConfig
     */
    protected $config;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(UserPasswordEncoderInterface $encoder, SecureConfig $config, EntityManager $manager)
    {
        $this->encoder      = $encoder;
        $this->config       = $config;
        $this->em           = $manager;
    }

    /**
     * @param BaseMembre $membre
     * @return BaseUser|null
     */
    public function findMembreLinkedUser(BaseMembre $membre) {

        return $this->getUserRepo()->createQueryBuilder('user')
            ->where('user.membre = :membre')
            ->setParameter('membre', $membre)
            ->getQuery()
            ->execute();
    }

    public function encodePassword(BaseUser $user, $password) {

        return $this->encoder->encodePassword($user, $password);
    }

    public function find($id) {

        return $this->getUserRepo()->find($id);
    }

    public function createUser(BaseUser $user) {

        $this->checkUsernameAndEmail($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function updateUser(BaseUser $user) {

        $this->checkUsernameAndEmail($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function deleteUser(BaseUser $user) {

        $this->em->remove($user);
        $this->em->flush();
    }

    public function buildUsername($base) {

        $i          = 0;
        $username   = $base;

        while($this->usernameTaken($username))
            $username = $base . $i++;

        return $username;
    }

    protected function usernameTaken(BaseUser $user) {

        $result = $this->getUserRepo()->findOneBy(array('username' => $user->getUsername()));
        return $result !== null && $result->getId() !== $user->getId();
    }

    protected function emailTaken(BaseUser $user) {

        if(empty($email))
            return false;

        $result = $this->getUserRepo()->findOneBy(array('email' => $user->getEmail()));
        return $result !== null && $result->getId() !== $user->getId();
    }

    protected function checkUsernameAndEmail(BaseUser $user) {

        if($this->emailTaken($user))
            throw new UserCreationException("Email already taken");

        if($this->usernameTaken($user))
            throw new UserCreationException("Username already taken");
    }

    protected function getUserRepo() {

        return $this->em->getRepository($this->config->getUserClass());
    }
}