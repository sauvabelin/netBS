<?php

namespace SauvabelinBundle\Command;

use SauvabelinBundle\Entity\BSGroupe;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PostInstallCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('netbs:post-install:sauvabelin')
            ->setDescription('Script post installation de la BS pour gestion de bails à l\'ancienne');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getContainer()->get('netbs.fichier.config');
        $em     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $io     = new SymfonyStyle($input, $output);

        $io->writeln("Importation des données WNG");
        $this->getApplication()->find('sauvabelin:import:wng')->run(new ArrayInput([]), $output);

        $io->writeln("Creation de la vue SQL nextcloud user-groups");
        $em->getConnection()->exec($this->getNextcloudUserGroupsViewSQL());

        $io->writeln("Creation de la vue SQL wiki");
        $em->getConnection()->exec($this->getWikiViewSQL());

        $io->writeln("Mise à jour des groupes");
        $groupes    = $em->getRepository($config->getGroupeClass())->findAll();

        /** @var BSGroupe $groupe */
        foreach($groupes as $groupe)
            $groupe->updateNCGroupName();

        $em->flush();
    }

    private function getNextcloudUserGroupsViewSQL() {
        return <<<EOT
CREATE OR REPLACE VIEW nextcloud_user_groups AS
SELECT u.username AS username, g.nc_group_name as groupname
FROM sauvabelin_netbs_users u
JOIN sauvabelin_netbs_membres m
  ON m.id = u.membre_id
JOIN netbs_fichier_attributions a
  ON a.membre_id = m.id
JOIN sauvabelin_netbs_groupes g
  ON a.groupe_id = g.id
WHERE a.dateDebut < NOW()
AND (a.dateFin IS NULL OR a.dateFin > NOW())
AND g.nc_group_name IS NOT NULL
AND u.nextcloud_account = TRUE
EOT;
    }

    private function getWikiViewSQL() {
        return <<<EOT
CREATE OR REPLACE VIEW wiki_users AS
SELECT u.username, u.password, u.salt, u.wiki_admin
FROM sauvabelin_netbs_users u
WHERE u.wiki_account = TRUE
EOT;
    }
}