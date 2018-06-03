<?php

namespace Sysmoh\DBTaskBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sysmoh\DBTaskBundle\Entity\Task;

class RunTasksCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sysmoh:dbtasks:run')
            ->setDescription('Runs some DBTasks')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addOption('amount', 'a', InputOption::VALUE_OPTIONAL, 'Amount of tasks to run', 10);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em         = $this->getContainer()->get('doctrine.orm.entity_manager');
        $manager    = $this->getContainer()->get('sysmoh.db_task.task_manager');
        $amount     = intval($input->getOption('amount'));
        $name       = $input->getArgument('name');
        $task       = $manager->getTask($name);
        $tasks      = $manager->getDBTasks($name, $amount, Task::WAITING);

        for($i = 0; $i < $amount && $i < count($tasks); ++$i) {

            try {

                $task->run($tasks[$i]->getParams());
                $tasks[$i]->setStatus(Task::SUCCESS);

            } catch (\Exception $e) {
                $tasks[$i]->setStatus(Task::FAILED);
            }

            $em->persist($tasks[$i]);
        }

        $em->flush();
    }
}
