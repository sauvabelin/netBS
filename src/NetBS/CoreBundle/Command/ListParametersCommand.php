<?php

namespace NetBS\CoreBundle\Command;

use NetBS\CoreBundle\Entity\Parameter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListParametersCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('netbs:debug:parameters')
            ->setDescription('Lists all registered parameters');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io         = new SymfonyStyle($input, $output);
        $manager    = $this->getContainer()->get('doctrine.orm.entity_manager');
        $bags       = $manager->getRepository('NetBSCoreBundle:ParameterBag')->findAll();
        $params     = [];

        /** @var Parameter $parameter */
        foreach($bags as $bag)
            foreach ($bag->getParameters() as $parameter)
                $params[]   = [
                    $bag->getName(),
                    $parameter->getKey(),
                    $parameter->getValue()
                ];

        $io->table(['bag', 'parameter', 'value'], $params);
    }
}
