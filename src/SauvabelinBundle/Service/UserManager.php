<?php

namespace SauvabelinBundle\Service;

use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Service\UserManager as UM;

class UserManager extends UM
{
    public function deleteUser(BaseUser $user)
    {
        $latestAccounts = $this->em->getRepository('SauvabelinBundle:LatestCreatedAccount')->findBy([
            'user' => $user
        ]);

        foreach($latestAccounts as $la)
            $this->em->remove($la);

        $this->em->flush();

        return parent::deleteUser($user);
    }
}