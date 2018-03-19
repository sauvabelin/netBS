<?php

namespace NetBS\SecureBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * Détermine si le nom d'utilisateur est déjà pris
     * @param $username
     * @return bool
     * SELECT * FROM secure_users WHERE username = {username}
     */
    public function usernameTaken($username) {

        return $this->findOneBy(array('username' => $username)) != null;
    }

    /**
     * Détermine si l'email d'un utilisateur est déjà pris
     * @param $email
     * @return bool
     * SELECT * FROM secure_users WHERE email = {email}
     */
    public function emailTaken($email) {

        return $this->findOneBy(array('username' => $email)) != null;
    }
}