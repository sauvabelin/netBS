<?php

namespace SauvabelinBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Entity\Fonction;

class LoadFonctions extends BSFixture
{
    public function load(ObjectManager $manager)
    {
        $data   = $this->loadYAML('fonctions.yml');

        foreach($data['fonctions'] as $name => $params) {

            $fonction   = new Fonction();
            $fonction
                ->setAbbreviation($params['abbreviation'])
                ->setNom($name)
                ->setPoids($params['poids']);

            $manager->persist($fonction);
        }

        $manager->flush();

        $updates    = [
            'fonction.commandant_id'            => 'commandant',
            'fonction.secretaire_general_id'    => 'secrétaire général',
            'fonction.eclaireur_id'             => 'éclaireur ou éclaireuse',
            'fonction.louveteau_id'             => 'louveteau ou louvette'
        ];

        foreach($updates as $key => $fn)
            $this->loadParameterWithId($manager, 'bs', $key,
                $manager->getRepository('NetBSFichierBundle:Fonction')->findOneBy(array('nom' => $fn)));
    }
}