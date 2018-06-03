<?php

namespace SauvabelinBundle\Service;

use SauvabelinBundle\Entity\BSUser;
use SauvabelinBundle\Entity\RuleMailingList;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class MailingListManager
{
    /**
     * @var ISPConfigManager
     */
    private $ispconfig;

    /**
     * @var ExpressionLanguage
     */
    private $el;

    public function __construct(ISPConfigManager $manager)
    {
        $this->ispconfig    = $manager;
    }

    /**
     * @param BSUser $user
     * @param RuleMailingList $list
     * @return bool
     */
    public function userBelongsTo(BSUser $user, RuleMailingList $list) {

        return (boolean)$this->getExpressionLanguage()
            ->evaluate($list->getElRule(), [
                'user' => $user
            ]);
    }

    /**
     * @return ExpressionLanguage
     */
    private function getExpressionLanguage() {

        if($this->el === null)
            $this->el = new ExpressionLanguage();

        return $this->el;
    }
}