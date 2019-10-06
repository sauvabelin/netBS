<?php

namespace TenteBundle\Voter;

use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Voter\NetBSVoter;
use TenteBundle\Entity\DrawingPart;
use TenteBundle\Entity\FeuilleEtat;
use TenteBundle\Entity\Reparation;
use TenteBundle\Entity\Tente;
use TenteBundle\Entity\TenteModel;

class TenteVoter extends NetBSVoter
{
    /**
     * Returns the class name(s) of the objects checked in this voter
     * @return string|array
     */
    protected function supportClass()
    {
        return [
            Tente::class,
            TenteModel::class,
            Reparation::class,
            FeuilleEtat::class,
            DrawingPart::class,
        ];
    }

    /**
     * Accept or denies the given crud operation on the given subject for the given user
     * @param string $operation a CRUD operation
     * @param \Object $subject
     * @param BaseUser $user
     * @return bool
     */
    protected function accept($operation, $subject, BaseUser $user)
    {
        // TODO: Implement accept() method.
    }
}