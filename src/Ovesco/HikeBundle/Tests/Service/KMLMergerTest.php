<?php

namespace Ovesco\HikeBundle\Tests\Service;

use Ovesco\HikeBundle\Service\KMLMerger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class KMLMergerTest extends WebTestCase
{
    public function testMerge()
    {
        $KMLMerger  = new KMLMerger();
        $KMLMerger->merge(
            __DIR__ . "/../data/kml1.kml",
            __DIR__ . "/../data/kml2.kml"
        );
    }
}
