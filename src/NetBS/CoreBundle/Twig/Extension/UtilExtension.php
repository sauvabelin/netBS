<?php

namespace NetBS\CoreBundle\Twig\Extension;

class UtilExtension extends \Twig_Extension
{
    protected $increment    = 0;

    public function getName()
    {
        return 'util';
    }

    public function getFunctions() {

        return [

            new \Twig_SimpleFunction('random_number', [$this, 'randomNumber']),
            new \Twig_SimpleFunction('increment', [$this, 'increment']),
            new \Twig_SimpleFunction('uniqid', [$this, 'uniqid']),
        ];
    }

    public function getFilters() {

        return [

            new \Twig_SimpleFilter('toBase64', [$this, 'base64encodeFilter']),
            new \Twig_SimpleFilter('fromBase64', [$this, 'base64decodeFilter'])
        ];
    }

    public function randomNumber($min = 0, $max = 99999) {

        return mt_rand($min, $max);
    }

    public function uniqid() {

        return uniqid();
    }

    public function increment() {

        return $this->increment++;
    }

    public function base64encodeFilter($value) {

        return base64_encode($value);
    }

    public function base64decodeFilter($value) {

        return base64_decode($value);
    }
}
