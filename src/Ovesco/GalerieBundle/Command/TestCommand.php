<?php

namespace Ovesco\GalerieBundle\Command;

use NetBS\CoreBundle\Utils\StrUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ovesco_galerie:test_command')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $path = Directory::unhashPath(Directory::);
        dump("R0FMRVJJRSBTY291dHMgbWFsZ3LDqSB0b3V0/R0FMRVJJRSBTb21lbyAoQlMgWFZJKQ==/V2Vlay1lbmRz/V2UgTm/Dq2wgMjAxOQ==");
        dump($this->hash());
        dump($this->hash2());
    }

    protected function hash() {
        $data = explode("/", "GALERIE Scouts malgré tout/GALERIE Someo (BS XVI)/Week-ends/We Noël 2019");
        $data = array_map(function($item) {return base64_encode(str_replace('?', '__intermark', $item));}, $data);

        return implode("/", $data);
    }

    protected function hash2($str) {
        $data = explode('/', $str);
        $data = array_map(function ($item) { return preg_replace("/^galerie-/", '', StrUtil::slugify($item));}, $data);
        return implode('/', $data);
    }
}
