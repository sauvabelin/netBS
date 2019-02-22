<?php

namespace Ovesco\FacturationBundle\Exporter;

use Doctrine\ORM\EntityManager;
use NetBS\CoreBundle\Exporter\PDFPreviewer;
use NetBS\CoreBundle\Model\ConfigurableExporterInterface;
use NetBS\CoreBundle\Model\ExporterInterface;
use NetBS\CoreBundle\Utils\Traits\ConfigurableExporterTrait;
use NetBS\FichierBundle\Mapping\BaseFamille;
use NetBS\FichierBundle\Mapping\BaseMembre;
use Ovesco\FacturationBundle\Entity\Compte;
use Ovesco\FacturationBundle\Entity\Creance;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Entity\FactureModel;
use Ovesco\FacturationBundle\Form\FactureConfigType;
use Ovesco\FacturationBundle\Model\FactureConfig;
use Ovesco\FacturationBundle\Util\BVR;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PDFFacture implements ExporterInterface, ConfigurableExporterInterface
{
    use ConfigurableExporterTrait;

    private $manager;

    private $engine;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->engine = new ExpressionLanguage();
    }

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
        return "Imprimer les factures";
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
        define('FPDF_FONTPATH', __DIR__ . '/Facture/fonts/');

        /** @var FactureConfig $config */
        $config = $this->configuration;
        $fpdf   = new \FPDF();
        $fpdf->SetLeftMargin($config->margeGauche);
        $fpdf->SetRightMargin($config->margeGauche);
        $fpdf->SetTopMargin($config->margeHaut);
        $fpdf->SetAutoPageBreak(true, 0);
        $fpdf->AddFont('OpenSans', '', 'OpenSans-Regular.php');
        $fpdf->AddFont('OpenSans', 'B', 'OpenSans-Bold.php');
        $fpdf->AddFont('Arial', '', 'arial.php');
        $fpdf->AddFont('BVR', '', 'ocrb10n.php');

        /** @var Facture[] $noAdress */
        $noAdress = [];
        foreach($items as $facture)
            if (!$facture->getDebiteur()->getSendableAdresse())
                $noAdress[] = $facture;

        if (count($noAdress) > 0) {
            $text = "Certaines factures sont adressées à des débiteurs n'ayant aucune adresse!\n" .
                "Les factures suivantes ne seront pas générées:\n";
            foreach($noAdress as $facture)
                $text .= " - {$facture->__toString()}, montant total: {$facture->getMontant()}\n";

            $fpdf->AddPage();
            $fpdf->SetFont('OpenSans', '', 10);
            $fpdf->MultiCell(200, 6, utf8_decode($text));
        }

        foreach($items as $facture)
            $this->printFacture($facture, $fpdf);

        // We've set impression date
        $this->manager->flush();

        return new StreamedResponse(function() use ($fpdf) {
            $fpdf->Output();
        });
    }

    private function getModel(Facture $facture) {

        $modelId = $this->configuration->model;
        if (is_int($modelId)) return $this->manager->getRepository('OvescoFacturationBundle:FactureModel')
            ->find($modelId);
        else {
            $models = $this->manager->getRepository('OvescoFacturationBundle:FactureModel')
                ->createQueryBuilder('m')->orderBy('m.poids')->getQuery()->getResult();

            /** @var FactureModel $item */
            foreach($models as $item)
                if ($this->evaluate($item->getApplicationRule(), $facture, false))
                    return $item;

            return $models[0];
        }
    }

    private function evaluate($string, Facture $facture, $parse = true) {

        if ($string === null) return true;

        if($parse) {
            $string = str_replace("\r", '', str_replace("\n", '', $string));
            $string = str_replace("'", "\\'", $string);
            $string = str_replace('[', "'~", str_replace("]", "~'", "'$string'"));
        }

        return $this->engine->evaluate($string, [
            'facture' => $facture,
            'debiteur' => $facture->getDebiteur(),
            'isFamille' => $facture->getDebiteur() instanceof BaseFamille
        ]);
    }

    private function printFacture(Facture $facture, \FPDF $fpdf) {

        if (!$facture->getDebiteur()->getSendableAdresse()) return;

        /** @var FactureConfig $config */
        $config = $this->configuration;
        $model = $this->getModel($facture);
        $date = $config->date instanceof \DateTime ? $config->date : $facture->getDate();

        $facture->setLatestImpression($date);
        $this->manager->persist($facture);

        $fpdf->AddPage();
        $debiteur = $facture->getDebiteur();
        $fpdf->Image(__DIR__ . '/Facture/logo.png', 15, 20, 16, 16);
        $fpdf->SetFont('OpenSans', 'B', 10);

        // Print adresse
        $fpdf->SetXY(35, 17);
        $fpdf->Cell(50, 10, $model->getGroupName());

        $fpdf->SetFont('OpenSans', '', 9);
        $fpdf->SetXY(35, 21);
        $fpdf->Cell(50, 10, utf8_decode($model->getRue()));

        $fpdf->SetXY(35, 25);
        $fpdf->Cell(50, 10, utf8_decode($model->getNpaVille()));

        // Print date and destinataire
        $fpdf->SetXY(130, 17);
        $printDate = $date->format('d') . " " .$this->toMois($date->format('m')) . " " . $date->format('Y');
        $fpdf->Cell(50, 10, utf8_decode($model->getCityFrom() . " le $printDate"));

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
        $fpdf->Cell(0, 20, utf8_decode(strtoupper($this->evaluate($model->getTitre(), $facture))));

        $fpdf->SetXY(15.2, 73);
        $fpdf->SetFont('OpenSans', '', 7);
        $fpdf->Cell(20, 10, 'N/Ref : ' . $facture->getFactureId());

        $fpdf->SetXY(15, 90);
        $fpdf->SetFontSize(10);
        $fpdf->MultiCell(0, $config->interligne, $this->evaluate($model->getTopDescription(), $facture), 0);
        $currentY = $fpdf->GetY() + 2;

        $fpdf->SetFontSize(9);

        $i = 0;
        /** @var Creance[] $creances */
        $creances = $facture->getCreances()->toArray();
        for(; $i < count($creances); $i++)
            $this->printCreanceLine($fpdf, $currentY, $i, $creances[$i]->getTitre(), $creances[$i]->getMontant());

        if(count($facture->getPaiements()) > 0)
            $this->printCreanceLine($fpdf, $currentY, $i++, "Montant déjà payé", -($facture->getMontantPaye()));

        if(count($creances) > 1 || count($facture->getPaiements()) > 0)
            $this->printCreanceLine($fpdf, $currentY, $i++, "Total", $facture->getMontantEncoreDu(), true);

        $currentY = $fpdf->GetY() + $config->interligne*2;

        $fpdf->SetFontSize(10);
        $fpdf->SetXY(15, $currentY);
        $fpdf->MultiCell(0, $config->interligne, $this->evaluate(utf8_decode($model->getBottomSalutations()), $facture));

        // Signature
        $fpdf->SetXY(130, $fpdf->GetY() + $config->interligne);
        $fpdf->Cell(50, 10, utf8_decode($model->getSignataire()));

        // Print BVR stuff
        $ref    = BVR::getReferenceNumber($facture);
        $ms     = $config->margeHaut;
        $mg     = $config->margeGauche;
        $haddr  = $ms + $config->haddr;
        $hg     = $ms + $config->hg;
        $waddr  = $mg + $config->waddr;
        $wg     = $mg - $config->wg;
        $wd     = $mg + $config->wd;
        $hd     = $ms + $config->hd;
        $wb     = $mg + $config->wb;
        $hb     = $ms + $config->hb;
        $il     = $config->bvrIl;
        $compte = $facture->getCompteToUse();

        //Adresse haut gauche
        $fpdf->SetFont('Arial', '', 9);
        $this->printBvrBsAdresse($fpdf, $wg, $haddr, $il, $compte);

        //Adresse haut droite
        $this->printBvrBsAdresse($fpdf, $waddr, $haddr, $il, $compte);

        //CCP
        $fpdf->SetFontSize(11);
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetXY($mg + $config->wccp , $ms + $config->hccp);
        $fpdf->Cell(50, $il, $compte->getCcp());

        //ligne codage gauche + adresse bas gauche
        $refNumber = sprintf("%s %s %s %s %s %s", substr($ref[1], 0, 2), substr($ref[1], 2, 5), substr($ref[1], 7, 5),
            substr($ref[1], 12, 5), substr($ref[1], 17, 5), substr($ref[1], 22, 5));
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetXY($wg , $hg);
        $fpdf->Cell(10, $il, $refNumber);
        $this->printBvrDebiteurAdresse($fpdf, $wg, $hg + $il, $il, $debiteur);

        //ligne codage droite
        $fpdf->SetFont('BVR', '', 11);
        $fpdf->SetXY($wd ,$hd);
        $fpdf->Cell(10, $il, $refNumber);


        //adresse bas droite
        $fpdf->SetFont('Arial', '', 9);
        $this->printBvrDebiteurAdresse($fpdf, $wd, $hd + $il*6, $il, $debiteur);

        //ligne codage bas
        $fpdf->SetFont('BVR', '', 12.8);
        $fpdf->SetXY($wb, $hb);
        $fpdf->Cell(0, $il, "$ref[0]>$ref[1]+ $ref[2]>");

        //Points de contoles visuels
        $fpdf->Line($wd+49,$hb-4.5,$wd+52,$hb-4.5);
        $fpdf->Line($wd-1.5,$hg+9,$wd-1.5,$hg+11);

    }

    private function printCreanceLine(\FPDF $fpdf, $baseY, $i, $titre, $montant, $bold = false) {

        if($bold) {
            $fpdf->SetFont('OpenSans', 'B');
        }

        $fpdf->SetXY(15, $baseY + ($i*6));
        $fpdf->Cell(0, 6, utf8_decode($titre), 1);

        $fpdf->SetXY(170, $baseY + ($i*6));
        $montant = number_format($montant, 2, '.', "'");
        $fpdf->Cell(0, 6, 'CHF ' . $montant, 'L', 'ln', 'R');

        if($bold) {
            $fpdf->SetFont('OpenSans', '');
        }
    }

    /**
     * @param \FPDF $fpdf
     * @param $x
     * @param $y
     * @param $interligne
     * @param BaseFamille|BaseMembre $debiteur
     */
    private function printBvrDebiteurAdresse(\FPDF $fpdf, $x, $y, $interligne, $debiteur) {

        $nom = $debiteur instanceof BaseFamille
            ? $debiteur->__toString()
            : $debiteur->getFamille()->getNom() . " " . $debiteur->getPrenom();

        $adresse = $debiteur->getSendableAdresse();

        $fpdf->SetXY($x , $y);
        $fpdf->Cell(10, $interligne, utf8_decode($nom));

        if($adresse) {
            $fpdf->SetXY($x, $y + $interligne);
            $fpdf->Cell(10, $interligne, utf8_decode($adresse->getRue()));
            $fpdf->SetXY($x, $y + $interligne * 2);
            $fpdf->Cell(10, $interligne, utf8_decode($adresse->getNpa(). ' ' . $adresse->getLocalite()));
        }
    }

    private function printBvrBsAdresse(\FPDF $fpdf, $x, $y, $interligne, Compte $compte) {
        $fpdf->SetXY($x, $y);
        $fpdf->Cell(50, $interligne, $compte->getLine1());
        $fpdf->SetXY($x , $y + $interligne);
        $fpdf->Cell(50, $interligne, $compte->getLine2());
        $fpdf->SetXY($x , $y + 2*$interligne);
        $fpdf->Cell(50, $interligne, $compte->getLine3());
        $fpdf->SetXY($x , $y + 3*$interligne);
        $fpdf->Cell(50, $interligne, $compte->getLine4());
    }

    /**
     * Returns the form used to configure the export
     * @return string
     */
    public function getConfigFormClass()
    {
        return FactureConfigType::class;
    }

    /**5
     * Returns the configuration object class
     * @return string
     */
    public function getConfigClass()
    {
        return FactureConfig::class;
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
            'Octobre', 'Novembre', 'Décembre'])[intval($mois) - 1];
    }
}
