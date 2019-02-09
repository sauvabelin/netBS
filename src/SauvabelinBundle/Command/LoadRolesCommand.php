<?php

namespace SauvabelinBundle\Command;

use Doctrine\ORM\EntityManager;
use NetBS\SecureBundle\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class LoadRolesCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sauvabelin:load_roles')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $roles = Yaml::parse(file_get_contents(__DIR__ . "/../Resources/structure/roles.yml"));
        $admin = $this->em->getRepository('NetBSSecureBundle:Role')->findOneBy(['role' => 'ROLE_ADMIN']);
        $roots = [];
        foreach($roles['roles'] as $name => $content)
            $roots[] = $this->loadRole($admin, $name, $content);
        $this->em->flush();
        $output->writeln('Ok: Roles loaded and updated');
    }

    private function loadRole($parent, $key, $given) {
        $role = $this->em->getRepository('NetBSSecureBundle:Role')->findOneBy(['role' => $key]);
        if (!$role) {
            $role = new Role($key, $given['poids'], $given['description']);
            $role->setParent($parent);
            $this->em->persist($role);
        }
        if (isset($given['children']))
            foreach($given['children'] as $childKey => $childGiven)
                $role->addChild($this->loadRole($role, $childKey, $childGiven));
        return $role;
    }
}
