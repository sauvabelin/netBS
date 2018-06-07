<?php

namespace GalerieBundle\Command;

use GalerieBundle\Model\NCNode;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumeNextcloudTaskCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('netbs:galerie:consume_nextcloud_task_command')
            ->setDescription('Consumes some nextcloud tasks')
            ->addArgument("amount", InputArgument::OPTIONAL, "Nombre de tâches à traiter", 20);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $amount     = intval($input->getArgument('amount'));
        $mapper     = $this->getContainer()->get('netbs.galerie.mapper');
        $pheanstalk = $this->getContainer()->get('pheanstalk');
        $tubes      = $pheanstalk->listTubes();

        if(!in_array('netbs.galerie.waiting', $tubes))
            return;

        $stats      = $pheanstalk->statsTube('netbs.galerie.waiting');
        $amount     = $amount > intval($stats['total-jobs']) ? intval($stats['total-jobs']) : $amount;
        $amount = 1;

        for($i = 0; $i < $amount; $i++) {

            $job = $pheanstalk->watch('netbs.galerie.waiting')
                ->ignore('default')
                ->reserve();

            dump($job->getData());

            $data   = json_decode($job->getData(), true);
            $node   = new NCNode($data);

            if(substr($node->getWebdavUrl(), 0, 14) === "files/galerie/") {

                if($data['type']  === "CREATED" || $data['type'] === "COPIED") {
                    $mapper->map($node);
                }

                if($data['type'] === "RENAMED") {
                    $mapper->update($node);
                }

                if($data['type'] === "DELETED") {
                    $mapper->remove($node);
                }
            }

            $pheanstalk->delete($job);
        }
    }
}
