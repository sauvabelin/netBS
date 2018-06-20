<?php

namespace NetBS\FichierBundle\Voter;

use NetBS\SecureBundle\Mapping\BaseUser;

class GroupeVoter extends FichierVoter
{
    /**
     * Returns the class name of the objects checked in this voter
     * @return string
     */
    protected function supportClass()
    {
        return $this->config->getGroupeClass();
    }

    protected function accept($operation, $subject, BaseUser $user)
    {
        foreach($user->getMembre()->getActivesAttributions() as $attribution) {

            if ($attribution->getGroupe()->getId() === $subject->getId()) {

                dump($attribution->getFonction()->getRoles());
                foreach ($attribution->getFonction()->getRoles() as $role) {

                    dump(str_replace("ROLE_", "", $role->getRole()));
                    dump(strtoupper($operation));
                    if (str_replace("ROLE_", "", $role->getRole()) === strtoupper($operation))
                        return true;
                }
            }
        }

        return false;
    }
}