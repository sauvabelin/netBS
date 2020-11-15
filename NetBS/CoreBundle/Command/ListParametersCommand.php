<?php

namespace NetBS\CoreBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use NetBS\CoreBundle\Entity\Parameter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListParametersCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

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
        $manager    = $this->entityManager;
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
