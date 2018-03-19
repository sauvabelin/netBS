<?php

namespace SauvabelinBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SauvabelinBundle\Entity\BSUser;
use SauvabelinBundle\Entity\RedirectMailingList;
use SauvabelinBundle\Entity\RuleMailingList;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class LoadMailingLists extends BSFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $mailingLists   = $this->loadYAML('mailing_lists.yml');
        $rules          = $mailingLists['mailing_lists_el'];
        $redirects      = $mailingLists['mailing_lists_redirect'];

        $elEngine       = new ExpressionLanguage();

        foreach($rules as $from => $elList) {

            $list   = new RuleMailingList();
            $elEngine->evaluate($elList['rule'], ['user' => new BSUser()]);

            $list->setFromAdresse($from);
            $list->setDescription($elList['description']);
            $list->setElRule($elList['rule']);

            $manager->persist($list);
        }

        foreach($redirects as $from => $redirectList) {

            $list   = new RedirectMailingList();

            $list->setFromAdresse($from);
            $list->setDescription($redirectList['description']);
            $list->setToAdresses($redirectList['to_addresses']);

            $manager->persist($list);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 10000;
    }
}