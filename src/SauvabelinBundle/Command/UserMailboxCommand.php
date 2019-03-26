<?php

namespace SauvabelinBundle\Command;

use SauvabelinBundle\Entity\BSUser;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class UserMailboxCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sauvabelin:mailing:generate-mailbox');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $isp = $this->getContainer()->get('sauvabelin.isp_config_manager');
        $params = $this->getContainer()->get('netbs.params');
        $listes = $em->getRepository('SauvabelinBundle:RuleMailingList')->findAll();
        $config = $this->getContainer()->get('netbs.secure.config');
        $users = $em->getRepository($config->getUserClass())->findAll();
        $el = new ExpressionLanguage();

        $weightRedirect = intval($params->getValue('bs', 'fonction.weight.mail_redirect'));
        $weightMailbox  = intval($params->getValue('bs', 'fonction.weight.mailbox'));

        /** @var BSUser $user */
        foreach($users as $user) {
            $bsAddress = $user->getUsername() . "@sauvabelin.ch";
            if (!$user->getMembre()) continue;
            $membre = $user->getMembre();
            $max = 0;
            foreach($membre->getActivesAttributions() as $attribution)
                if ($max < $attribution->getFonction()->getPoids())
                    $max = $attribution->getFonction()->getPoids();


            // First retrieve anything we got for user
            $mailbox = $isp->getMailbox($bsAddress);
            $transfer = $isp->getMailingList($bsAddress);

            // Needs a mailbox cause doesnt have one
            if ($max > $weightMailbox && !$mailbox) {
                // TODO uncomment
                $user->setNewPasswordRequired(true);
            }

            // Dude needs a mailbox
            if ($max >= $weightMailbox) {
                if ($user->getEmailBS() === BSUser::HAS_ACCOUNT) continue; // already has an account
                else if ($user->getEmailBS() === BSUser::HAS_REDIRECT) {
                    dump($isp->getMailbox($bsAddress));
                }
                else {
                    // No information, first, try to check if he has anything, might be an error

                    if ($transfer) { // remove

                    }

                    if ($mailbox) $user->setEmailBS(BSUser::HAS_ACCOUNT); // he got a mailbox

                }
            }
            die;
        }
    }
}
