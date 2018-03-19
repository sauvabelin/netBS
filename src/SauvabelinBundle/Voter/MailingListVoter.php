<?php

namespace SauvabelinBundle\Voter;

use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Voter\NetBSVoter;
use SauvabelinBundle\Entity\RedirectMailingList;
use SauvabelinBundle\Entity\RuleMailingList;

class MailingListVoter extends NetBSVoter
{

    /**
     * Returns the class name(s) of the objects checked in this voter
     * @return string|array
     */
    protected function supportClass()
    {
        return [
            RedirectMailingList::class,
            RuleMailingList::class
        ];
    }

    /**
     * Accept or denies the given crud operation on the given subject for the given user
     * @param string $operation a CRUD operation
     * @param $subject
     * @param BaseUser $user
     * @return bool
     */
    protected function accept($operation, $subject, BaseUser $user)
    {
        return false;
    }
}