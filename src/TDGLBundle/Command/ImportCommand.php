<?php

namespace TDGLBundle\Command;

use NetBS\FichierBundle\Entity\Adresse;
use NetBS\FichierBundle\Entity\Attribution;
use NetBS\FichierBundle\Entity\ContactInformation;
use NetBS\FichierBundle\Entity\Email;
use NetBS\FichierBundle\Entity\Fonction;
use NetBS\FichierBundle\Entity\Telephone;
use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Mapping\BaseMembre;
use NetBS\FichierBundle\Mapping\Personne;
use NetBS\FichierBundle\Service\FichierConfig;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TDGLBundle\Entity\TDGLFamille;
use TDGLBundle\Entity\TDGLMembre;

class ImportCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tdgl:import')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $poolFamilles = [];
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $fonctions = $em->getRepository('NetBSFichierBundle:Fonction')->findAll();
        $groupes = $em->getRepository('NetBSFichierBundle:Groupe')->findAll();
        $data = array_map(function($item) {return array_map(function($e) {return $this->e($e);}, explode(';', $item));}, explode("\n", file_get_contents(__DIR__ . "/../Resources/imports/Book1.csv")));

        foreach($data as $item) {

            if (count($item) === 1) continue;

            list ($sexeL, $familleId, $unite, $fnName, $totem, $nom, $prenom, $adresse, $npa, $ville, $telParents, $telGars, $naissance, $mailParents, $entree) = $item;

            $sexe = $sexeL === 'h' ? Personne::HOMME : Personne::FEMME;
            $groupe = array_values(array_filter($groupes, function($grp) use ($unite) {
                return strtolower($grp->getNom()) === strtolower($unite);
            }));

            $fonction = array_filter($fonctions, function(Fonction $fn) use ($fnName) {
                $fns = array_map(function($i) { return strtolower($i); }, explode('/', $fnName));
                return in_array(strtolower($fn->getAbbreviation()), $fns);
            });

            $famille = null;

            if ($familleId && isset($poolFamilles[$familleId])) {
                $famille = $poolFamilles[$familleId];

            } else {

                $famille = new TDGLFamille();
                $famille->setNom($nom);
                $famille->setContactInformation(new ContactInformation());
                $famille->setValidity(BaseFamille::VALIDE);

                if ($adresse) {
                    $addr = new Adresse();
                    $addr->setNpa($npa)->setRue($adresse)->setLocalite($ville);
                    $famille->addAdresse($addr);
                }

                if ($telParents) {
                    $famille->addTelephone(new Telephone($telParents));
                }

                if ($mailParents) {
                    $famille->addEmail(new Email($mailParents));
                }
            }

            $membre = new TDGLMembre();
            $membre->setPrenom($prenom);
            $membre->setSexe($sexe);
            $membre->setStatut(BaseMembre::INSCRIT);
            $membre->setContactInformation(new ContactInformation());
            $membre->setNaissance($this->date($naissance));
            if ($entree)
                $membre->setInscription($this->date($entree));
            $membre->setTotem($totem);
            if ($telGars)
                $membre->addTelephone(new Telephone($telGars));

            foreach($fonction as $fn) {
                $attribution = new Attribution();
                $attribution->setGroupe($groupe[0]);
                $attribution->setFonction($fn);
                $membre->addAttribution($attribution);
            }

            $famille->addMembre($membre);
            $em->persist($famille);
        }

        $em->flush();
    }

    private function e($val) {
        return empty($val) ? null : $val;
    }

    private function date($input) {
        if (empty($input)) return null;
        return \DateTime::createFromFormat('d.m.Y', $input);
    }
}
