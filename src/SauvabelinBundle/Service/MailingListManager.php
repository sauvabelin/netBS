<?php

namespace SauvabelinBundle\Service;

use Doctrine\ORM\EntityManager;
use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Service\SecureConfig;
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
     * @var EntityManager
     */
    private $manager;

    /**
     * @var SecureConfig
     */
    private $config;

    /**
     * @var ExpressionLanguage
     */
    private $el;

    public function __construct(ISPConfigManager $ispconfig, EntityManager $manager, SecureConfig $config)
    {
        $this->ispconfig    = $ispconfig;
        $this->manager      = $manager;
        $this->config       = $config;
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

    public function getCurrentRuleMailingListEmails(RuleMailingList $list) {

        $content = $this->ispconfig->getMailingList($list->getFromAdresse());
        if ($content === null) return [];
        return explode("\r\n", $content['destination']);
    }

    public function getFreshRuleMailingListEmails(RuleMailingList $list) {

        /** @var BaseUser[] $users */
        $users = $this->manager->getRepository($this->config->getUserClass())->findAll();

        $inners = array_filter($users, function (BaseUser $user) use ($list) {
            return $this->getExpressionLanguage()->evaluate($list->getElRule(), ['user' => $user]);
        });

        $addresses = array_unique(array_values(array_filter(array_map(function(BaseUser $user) {
            return $user->getSendableEmail();
        }, $inners), function($str) { return !empty($str); })));

        return $addresses;
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
