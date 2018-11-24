<?php

namespace Ovesco\FacturationBundle\Util;
use Ovesco\FacturationBundle\Entity\Facture;

/**
 * Class BVR
 * @package Hochet\FacturationBundle\Service
 * Génère les informations requises pour emettre un BVR. Ce code est integralement basé sur l'ancien fichier BS
 */

class BVR
{
    /**
     * Détermine le numéro de référence associé à la facture passée
     * @param Facture $facture
     * @return array
     */
    public static function getReferenceNumber(Facture $facture){

        $chaine     = '04';
        $chaine    .= self::getControlNumber($chaine);
        $tab[0]     = $chaine;

        $chaine     = sprintf("%026s", $facture->getId());
        $chaine    .= self::getControlNumber($chaine);
        $tab[1]     = $chaine;

        //CCP
        $tab[2]     = str_replace("-", "", $facture->getCompteToUse()->getCcp());
        return $tab;
    }

    /**
     * Generate a control number based on the given id, between 0 and 9
     * Basé sur l'ancien fichier BS pour ne pas perdre la cohérence, méthode un peu foireuse
     * @param  int $id
     * @return int
     */
    public static function getControlNumber($id) {

        $report     = 0;
        $cpt        = strlen($id);

        for($i = 0; $i < $cpt; $i++)
            $report = substr(self::getReportLine($report), substr($id, $i, 1), 1);

        return (10 - $report) % 10;
    }

    /**
     * @param $report
     * Pris de l'ancien fichier BS
     * @return bool|string
     */
    public static function getReportLine($report) {

        $etalon         = "09468271350946827135";
        $lignereport    = substr($etalon, $report, 10);

        return $lignereport;
    }
}