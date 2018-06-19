<?php

namespace Ovesco\HikeBundle\Service;

class KMLMerger
{
    private $kmls   = [];

    public function addKml($kmlPath) {

        $this->kmls[] = $kmlPath;
    }

    public function merge() {

        $mainDom    = new \DOMDocument();
        $mainDom->load($this->kmls[0]);
        $document   = $mainDom->getElementsByTagName("Document")->item(0);

        for($i = 1; $i < count($this->kmls); $i++) {

            $dom        = new \DOMDocument();
            $dom->load($this->kmls[$i]);

            foreach($dom->getElementsByTagName("Placemark") as $item) {

                $item   = $mainDom->importNode($item);
                $document->appendChild($item);
            }
        }

        return $mainDom;
    }
}