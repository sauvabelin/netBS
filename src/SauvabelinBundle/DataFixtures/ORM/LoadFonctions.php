<?php

namespace SauvabelinBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use NetBS\FichierBundle\Entity\Fonction;
use NetBS\SecureBundle\Entity\Role;

class LoadFonctions extends BSFixture
{
    public function load(ObjectManager $manager)
    {
        $data   = $this->loadYAML('fonctions.yml');
        $roles  = [];

        /** @var Role $role */
        foreach($manager->getRepository('NetBSSecureBundle:Role')->findAll() as $role)
            $roles[$role->getRole()] = $role;

        foreach($data['fonctions'] as $name => $params) {

            $fonction   = new Fonction();
            $fonction
                ->setAbbreviation($params['abbreviation'])
                ->setNom($name)
                ->setPoids($params['poids']);

            foreach($params['roles'] as $requiredRole)
                $fonction->addRole($roles[$requiredRole]);

            $manager->persist($fonction);
        }

        $manager->flush();

        $updates    = [
            'fonction.commandant_id'            => 'commandant',
            'fonction.secretaire_general_id'    => 'secrétaire général',
            'fonction.eclaireur_id'             => 'éclaireur ou éclaireuse',
            'fonction.louveteau_id'             => 'louveteau ou louvette',
            'fonction.cp_id'                    => 'chef de patrouille',
            'fonction.cl_id'                    => 'chef louveteaux/louvettes',
            'fonction.rouge_id'                 => 'routier'
        ];

        foreach($updates as $key => $fn)
            $this->loadParameterWithId($manager, 'bs', $key,
                $manager->getRepository('NetBSFichierBundle:Fonction')->findOneBy(array('nom' => $fn)));
    }

    public function getOrder()
    {
        return 500;
    }
}