<?php

namespace NetBS\SecureBundle\Voter;

use NetBS\SecureBundle\Mapping\BaseUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter as RV;

class RoleVoter extends RV
{
    public function extractRoles(TokenInterface $token)
    {
        return $token->getUser() instanceof BaseUser ? $token->getUser()->getAllRoles() : [];
    }
}