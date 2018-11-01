<?php

namespace SauvabelinBundle\Command;

use SauvabelinBundle\Entity\BSGroupe;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Script post installation de la BS pour gestion de bails à l\'ancienne')
            ->addOption('purge', null, InputOption::VALUE_OPTIONAL, 'If set to true, purge database')
            ->addOption('dummy', null, InputOption::VALUE_OPTIONAL, 'If set to true, loads some dummy data');
    }

    protected function getBoolValue($val) {

        if(is_bool($val))
            return $val;

        if($val === null)
            return null;

        if($val === "false" || $val === "0")
            return false;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getContainer()->get('netbs.fichier.config');
        $em     = $this->getContainer()->get('doctrine.orm.entity_manager');
        $io     = new SymfonyStyle($input, $output);
        $dummy  = $this->getBoolValue($input->getOption('dummy'));

        if(!$dummy) {
            $io->writeln("Importation des données WNG");
            $this->getApplication()->find('sauvabelin:import:wng')->run(new ArrayInput([]), $output);
        }

        $io->writeln("Creation de la vue SQL nextclouf groups");
        $em->getConnection()->exec($this->getNextcloudGroupsViewSQL());

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

        //Mise à jour correcte des attributions des clans et des rouges
        $clans  = $em->getRepository('SauvabelinBundle:BSGroupe')->findOneBy(array('nom' => 'quatrième branche'))
            ->getEnfants();

        $rouges = $em->getRepository('SauvabelinBundle:BSGroupe')->findOneBy(array('nom' => 'troisième branche'))
            ->getEnfants();

        $rouge  = $em->getRepository('NetBSFichierBundle:Fonction')->findOneBy(array('abbreviation' => 'rouge'));
        $gris   = $em->getRepository('NetBSFichierBundle:Fonction')->findOneBy(array('abbreviation' => 'membre clan'));

        /** @var BSGroupe $clan */
        foreach($clans as $clan)
            foreach($clan->getActivesAttributions() as $attribution)
                if($attribution->getFonction()->getNom() === "routier")
                    $attribution->setFonction($gris);

        /** @var BSGroupe $troupe */
        foreach($rouges as $troupe)
            foreach($troupe->getActivesAttributions() as $attribution)
                if($attribution->getFonction()->getNom() === "éclaireur ou éclaireuse")
                    $attribution->setFonction($rouge);

        $em->flush();
    }

    private function getNextcloudGroupsViewSQL() {

        return <<<EOT
CREATE OR REPLACE VIEW nextcloud_groups AS
SELECT g.id AS group_id, g.nom, g.nc_group_name FROM sauvabelin_netbs_groupes g
INNER JOIN netbs_fichier_groupe_types gt
	ON g.groupeType_id = gt.id
WHERE gt.id IN (
	SELECT p.value FROM netbs_core_parameters p
    WHERE p.namespace = "bs"
    AND p.paramKey IN (
		"groupe_type.troupe_id",
        "groupe_type.meute_id", 
        "groupe_type.clan_id",
        "groupe_type.association_id",
        "groupe_type.edc_id",
        "groupe_type.equipe_interne_id",
        "groupe_type.branche_id"
	)
);
EOT;

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
JOIN nextcloud_groups g
  ON (a.groupe_id = g.group_id) OR (
  	(SELECT parent_id FROM sauvabelin_netbs_groupes sng WHERE sng.id = a.groupe_id) = g.group_id
	AND a.fonction_id IN (
		SELECT pr.value FROM netbs_core_parameters pr
        WHERE pr.namespace = "bs"
        AND pr.paramKey IN (
			"fonction.cl_id",
            "fonction.cp_id"
        )
	)
)
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