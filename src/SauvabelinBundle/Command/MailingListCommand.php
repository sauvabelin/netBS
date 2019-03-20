<?php

namespace SauvabelinBundle\Command;

use SauvabelinBundle\Entity\RuleMailingList;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class MailingListCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sauvabelin:mailing-lists:map');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $isp = $this->getContainer()->get('sauvabelin.isp_config_manager');
        $listes = $em->getRepository('SauvabelinBundle:RuleMailingList');
        $config = $this->getContainer()->get('netbs.secure.config');
        $users = $em->getRepository($config->getUserClass())->findAll();
        $el = new ExpressionLanguage();

        /** @var RuleMailingList $liste */
        foreach($listes as $liste) {
            $address = $isp->getMailingList($liste->getFromAdresse());
        }
    }
}
