<?php

namespace Ovesco\FacturationBundle\Exporter;

use NetBS\CoreBundle\Exporter\Config\FPDFConfig;
use NetBS\CoreBundle\Exporter\PDFPreviewer;
use NetBS\CoreBundle\Form\PDFConfig\FPDFType;
use NetBS\CoreBundle\Model\ConfigurableExporterInterface;
use NetBS\CoreBundle\Model\ExporterInterface;
use NetBS\CoreBundle\Utils\Traits\ConfigurableExporterTrait;
use NetBS\FichierBundle\Mapping\BaseFamille;
use Ovesco\FacturationBundle\Entity\Creance;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Util\BVR;
use Symfony\Component\HttpFoundation\StreamedResponse;

define('FPDF_FONTPATH', __DIR__ . '/Facture/fonts/');

class PDFFacture implements ExporterInterface, ConfigurableExporterInterface
{
    use ConfigurableExporterTrait;

    /**
     * Returns an alias representing this exporter
     * @return string
     */
    public function getAlias()
    {
        return 'pdf.factures';
    }

    /**
     * Returns the exported item's class
     * @return string
     */
    public function getExportableClass()
    {
        return Facture::class;
    }

    /**
     * Returns a displayable name of this exporter
     * @return string
     */
    public function getName()
    {
        return "PDF Factures";
    }

    /**
     * Returns this exporter category, IE pdf, excel...
     * @return string
     */
    public function getCategory()
    {
        return 'pdf';
    }

    /**
     * Returns a valid response to be returned directly
     * @param Facture[] $items
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export($items)
    {
        $fpdf   = new \FPDF();
        $fpdf->SetLeftMargin(15);
        $fpdf->SetRightMargin(15);
        $fpdf->SetAutoPageBreak(true, 0);
        $fpdf->AddFont('OpenSans', '', 'OpenSans-Regular.php');
        $fpdf->AddFont('OpenSans', 'B', 'OpenSans-Bold.php');
        $fpdf->AddFont('Arial', '', 'arial.php');
        $fpdf->AddFont('BVR', '', 'ocrb10n.php');

        foreach($items as $facture)
            $this->printFacture($facture, $fpdf);

        return new StreamedResponse(function() use ($fpdf) {
            $fpdf->Output();
        });
    }

    private function printFacture(Facture $facture, \FPDF $fpdf) {

        $fpdf->AddPage();
        $debiteur = $facture->getDebiteur();
        $fpdf->Image(__DIR__ . '/Facture/logo.png', 15, 20, 16, 16);
        $fpdf->SetFont('OpenSans', 'B', 10);

        // Print adresse
        $fpdf->SetXY(35, 17);
        $fpdf->Cell(50, 10, 'Brigade de Sauvabelin');

        $fpdf->SetFont('OpenSans', '', 9);
        $fpdf->SetXY(35, 21);
        $fpdf->Cell(50, 10, utf8_decode('Le quartier-maître'));

        $fpdf->SetXY(35, 25);
        $fpdf->Cell(50, 10, utf8_decode('Case Postale 5455'));

        $fpdf->SetXY(35, 29);
        $fpdf->Cell(50, 10, utf8_decode('1002 Lausanne'));

        // Print date and destinataire
        $fpdf->SetXY(130, 17);
        $mois = $this->toMois($facture->getDate()->format('m'));
        $date = $facture->getDate()->format('d') . " $mois " . $facture->getDate()->format('Y');
        $fpdf->Cell(50, 10, utf8_decode("Lausanne le $date"));

        $adresse    = $facture->getDebiteur()->getSendableAdresse();

        if($adresse) {

            $fpdf->SetXY(130, 33);
            $fpdf->Cell(50, 10, $facture->getDebiteur()->__toString());

            $fpdf->SetXY(130, 37);
            $fpdf->Cell(50, 10, utf8_decode($adresse->getRue()));

            $fpdf->SetXY(130, 41);
            $fpdf->Cell(50, 10, $adresse->getNpa() . " " . utf8_decode($adresse->getLocalite()));

        }

        // Print title
        $fpdf->SetXY(15, 60);
        $fpdf->SetFont('OpenSans', 'B', 20);
        $fpdf->Cell(0, 20, utf8_decode(strtoupper("Facture stylee")));

        $fpdf->SetXY(15.2, 73);
        $fpdf->SetFont('OpenSans', '', 7);
        $fpdf->Cell(20, 10, 'N/Ref : ' . $facture->getId());

        $fpdf->SetXY(15, 90);
        $fpdf->SetFontSize(10);
        $fpdf->Cell(0, 30, '', 1);

        $fpdf->SetFontSize(9);

        /** @var Creance[] $creances */
        $creances = array_merge($facture->getCreances()->toArray(), $facture->getCreances()->toArray());
        for($i = 0; $i < count($creances); $i++) {

            $fpdf->SetXY(15, 125 + ($i*6));
            $fpdf->Cell(0, 6, $creances[$i]->getTitre(), 1);

            $fpdf->SetXY(170, 125 + ($i*6));
            $montant = number_format($creances[$i]->getMontant(), 2, '.', "'");
            $fpdf->Cell(0, 6, 'CHF ' . $montant, 'L', 'ln', 'R');
        }

        $fpdf->SetXY(15, 130 + count($creances)*7);
        $fpdf->Cell(0, 30, '', 1);


        // Print BVR stuff
        $nom    = $debiteur instanceof BaseFamille ? $debiteur->__toString() : $debiteur->getFamille()->getNom() . " " . $debiteur->getPrenom();
        $ref    = BVR::getReferenceNumber($facture);
        $ms     = 10;
        $mg     = 12.3;
        $haddr  = 200;
        $hg     = $ms + 248;
        $waddr  = $mg + 56;
        $wg     = $mg - 8;
        $wd     = $mg + 115;
        $hd     = $ms + 212;
        $wb     = $mg + 83;
        $hb     = $ms + 266;
        $il     = 4;
        $compte = $facture->getCompteToUse();

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetXY($wg , $haddr);
        $fpdf->Cell(50, $il, $compte->getLine1());
        $fpdf->SetXY($wg , $haddr + $il);
        $fpdf->Cell(50, $il, $compte->getLine2());
        $fpdf->SetXY($wg , $haddr + 2*$il);
        $fpdf->Cell(50, $il, $compte->getLine3());

        //Adresse haut droite
        $fpdf->SetXY($waddr , $haddr);
        $fpdf->Cell(50, $il, $compte->getLine1());
        $fpdf->SetXY($waddr , $haddr + $il);
        $fpdf->Cell(50, $il, $compte->getLine2());
        $fpdf->SetXY($waddr , $haddr + $il*2);
        $fpdf->Cell(50, $il, $compte->getLine3());

        //CCP
        $fpdf->SetFontSize(11);
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetXY($mg + 77 , $ms + 221);
        $fpdf->Cell(50, $il, $compte->getCcp());

        //ligne codage gauche + adresse bas gauche
        $refNumber = sprintf("%s %s %s %s %s %s", substr($ref[1], 0, 2), substr($ref[1], 2, 5), substr($ref[1], 7, 5),
            substr($ref[1], 12, 5), substr($ref[1], 17, 5), substr($ref[1], 22, 5));
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetXY($wg , $hg);
        $fpdf->Cell(10, $il, $refNumber);

        $fpdf->SetXY($wg , $hg + $il);
        $fpdf->Cell(10, $il, $nom);

        if($adresse) {
            $fpdf->SetXY($wg, $hg + $il * 2);
            $fpdf->Cell(10, $il, $adresse->getRue());
            $fpdf->SetXY($wg, $hg + $il * 3);
            $fpdf->Cell(10, $il, $adresse->getNpa(). ' ' . $adresse->getLocalite());
        }


        //ligne codage droite
        $fpdf->SetFont('BVR', '', 11);
        $fpdf->SetXY($wd ,$hd);
        $fpdf->Cell(10, $il, $refNumber);


        //adresse bas droite
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetXY($wd , $hd + $il*6);
        $fpdf->Cell(10, $il, $nom);

        if($adresse) {
            $fpdf->SetXY($wd, $hd + $il * 7);
            $fpdf->Cell(10, $il, $adresse->getRue());
            $fpdf->SetXY($wd, $hd + $il * 8);
            $fpdf->Cell(10, $il, $adresse->getNpa() . ' ' . $adresse->getLocalite());
        }

        //ligne codage bas
        $fpdf->SetFont('BVR', '', 12.8);
        $fpdf->SetXY($wb, $hb);
        $fpdf->Cell(0, $il, "$ref[0]>$ref[1]+ $ref[2]>");

        //Points de contoles visuels
        $fpdf->Line($wd+49,$hb-4.5,$wd+52,$hb-4.5);
        $fpdf->Line($wd-1.5,$hg+9,$wd-1.5,$hg+11);

    }

    /**
     * Returns the form used to configure the export
     * @return string
     */
    public function getConfigFormClass()
    {
        return FPDFType::class;
    }

    /**5
     * Returns the configuration object class
     * @return string
     */
    public function getConfigClass()
    {
        return FPDFConfig::class;
    }

    /**
     * If the rendered file can be previewed, return the used
     * previewer class
     * @return string
     */
    public function getPreviewer()
    {
        return PDFPreviewer::class;
    }

    private function toMois($mois) {
        return (['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre',
            'Octobre', 'Novembre', 'Décembre'])[intval($mois)];
    }
}