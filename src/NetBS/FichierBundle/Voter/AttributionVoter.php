<?php

namespace NetBS\FichierBundle\Voter;

use NetBS\SecureBundle\Mapping\BaseUser;

class AttributionVoter extends GroupeVoter
{
    /**
     * Returns the class name of the objects checked in this voter
     * @return string
     */
    protected function supportClass()
    {
        return $this->config->getAttributionClass();
    }

    protected function accept($operation, $subject, BaseUser $user)
    {

        foreach($subject->getMembre()->getActivesAttributions() as $attribution)
            if(parent::accept($operation, $attribution->getGroupe(), $user))
                return true;

        return false;
    }
}