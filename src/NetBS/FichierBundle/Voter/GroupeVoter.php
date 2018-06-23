<?php

namespace NetBS\FichierBundle\Voter;

use NetBS\FichierBundle\Mapping\BaseGroupe;
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

    /**
     * @param string $operation
     * @param BaseGroupe $subject
     * @param BaseUser $user
     * @return bool
     */
    protected function accept($operation, $subject, BaseUser $user)
    {
        while($subject !== null) {
            foreach ($user->getMembre()->getActivesAttributions() as $attribution) {

                if ($attribution->getGroupe()->getId() === $subject->getId()) {

                    foreach ($attribution->getFonction()->getRoles() as $role) {
                        if (str_replace("ROLE_", "", $role->getRole()) === strtoupper($operation))
                            return true;
                    }
                }
            }

            $subject = $subject->getParent();
        }

        return false;
    }
}