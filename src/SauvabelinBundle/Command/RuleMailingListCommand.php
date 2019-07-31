<?php

namespace SauvabelinBundle\Command;

use NetBS\SecureBundle\Mapping\BaseUser;
use SauvabelinBundle\Entity\RuleMailingList;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class RuleMailingListCommand extends ContainerAwareCommand
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

        $contt = ($isp->getMailingList('embs@sauvabelin.ch'));
        $contt = explode("\r\n", $contt['destination']);
        dump($contt);
        die;
        die;
        $listes = $em->getRepository('SauvabelinBundle:RuleMailingList')->findAll();
        $config = $this->getContainer()->get('netbs.secure.config');
        $users = $em->getRepository($config->getUserClass())->findAll();
        $el = new ExpressionLanguage();


        /** @var RuleMailingList $liste */
        foreach($listes as $liste) {
            if ($liste->getFromAdresse() === 'embs@sauvabelin.ch') {
                $inners = array_filter($users, function (BaseUser $user) use ($el, $liste) {
                    return $el->evaluate($liste->getElRule(), ['user' => $user]);
                });

                $addresses = array_values(array_filter(array_map(function(BaseUser $user) {
                    return $user->getSendableEmail();
                }, $inners), function($str) { return !empty($str); }));
                dump($addresses);
                // $address = $isp->getMailingList($liste->getFromAdresse());
                die;
            }
        }
    }

    private function parseList($liste) {
        return array_filter(explode("\r\n", $liste), function($str) {
            return strlen($str) > 13;
        });
    }
}
