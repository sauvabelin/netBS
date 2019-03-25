<?php

namespace Ovesco\FacturationBundle\Exporter;

use NetBS\CoreBundle\Exporter\PDFPreviewer;
use Ovesco\FacturationBundle\Entity\Facture;
use Ovesco\FacturationBundle\Form\QrFactureConfigType;
use Ovesco\FacturationBundle\Model\QrFactureConfig;
use Sprain\SwissQrBill\DataGroup\Element\CombinedAddress;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\Reference\QrPaymentReferenceGenerator;

class PDFQrFacture extends BaseFactureExporter
{
    const PART_HEIGHT = 105;
    const A4_WIDTH = 210;
    const A4_HEIGHT = 297;
    const PAYMENT_WIDTH = 148;
    const DEBTOR_WIDTH = 62;
    const PART_MARGIN = 5;

    /**
     * Returns an alias representing this exporter
     * @return string
     */
    public function getAlias()
    {
        return 'pdf.qr.factures';
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
        return "Imprimer les factures format QR";
    }

    /**
     * Returns this exporter category, IE pdf, excel...
     * @return string
     */
    public function getCategory()
    {
        return 'pdf';
    }

    protected function printDetails(Facture $facture, \FPDF $fpdf) {

        $this->printReceiptPart($facture, $fpdf);
        $this->printPaymentPart($facture, $fpdf);
    }

    private function printReceiptPart(Facture $facture, \FPDF $fpdf) {

        $top = self::A4_HEIGHT - self::PART_HEIGHT;
        $margin = self::PART_MARGIN;
        $left = 0 + $margin;

        $fpdf->SetXY(0, $top);
        $fpdf->Cell(self::DEBTOR_WIDTH, self::PART_HEIGHT, '', $this->getConfiguration()->border);

        $compte = $facture->getCompteToUse();
        $debiteur = $facture->getDebiteur();
        $adresse = $debiteur->getSendableAdresse();

        if ($this->getConfiguration()->border) {
            $fpdf->SetDrawColor(255,0,0);
            $fpdf->SetXY($left, $top + $margin);
            $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 7, '', 1);

            $fpdf->SetXY($left, $top + $margin + 7);
            $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 56, '', 1);

            $fpdf->SetXY($left, $top + $margin + 7 + 56);
            $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 14, '', 1);

            $fpdf->SetXY($left, $top + $margin + 7 + 56 + 14);
            $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 18, '', 1);
        }

        // TITRE
        $fpdf->SetXY($left, $top + $margin);
        $fpdf->SetFont('Arial', 'B', 11);
        $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 7, 'Receipt');

        // payable to
        $fpdf->SetXY($left, $top + $margin + 7);
        $fpdf->SetFont('Arial', 'B', 6);
        $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 5, 'Account / Payable to');


        $fpdf->SetXY($left, $top + $margin + 11);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->MultiCell(self::DEBTOR_WIDTH - 2*$margin, 4, implode("\n", [
            'CH56 1234 5678 0034 5453 5', //utf8_decode($compte->getIban()),
            utf8_decode($compte->getLine1()),
            utf8_decode($compte->getLine2()),
            utf8_decode($compte->getLine3()),
        ]));

        // Reference
        $fpdf->SetXY($left, $top + $margin + 28);
        $fpdf->SetFont('Arial', 'B', 6);
        $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 9, 'Reference');
        $fpdf->SetXY($left, $top + $margin + 34);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 4, '12 23456 67890 54546 67675 53689');

        // Payable by
        $fpdf->SetXY($left, $top + $margin + 38);
        $fpdf->SetFont('Arial', 'B', 6);
        $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 9, 'Payable by');

        $fpdf->SetXY($left, $top + $margin + 44);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->MultiCell(self::DEBTOR_WIDTH - 2*$margin, 4, implode("\n", [
            utf8_decode($debiteur->__toString()),
            utf8_decode($adresse->getRue()),
            utf8_decode($adresse->getNpa() . ' ' . $adresse->getLocalite())
        ]));

        // Currency
        $fpdf->SetXY($left, $top + 7 + 56 + $margin);
        $fpdf->SetFont('Arial', 'B', 6);
        $fpdf->Cell(11, 5, 'Currency');
        $fpdf->SetXY($left + 11, $top + 7 + 56 + $margin);
        $fpdf->Cell(10, 5, 'Amount');

        $fpdf->SetXY($left, $top + 7 + 56 + $margin + 4);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(12, 5, 'CHF');

        // draw user put amount
        $x = self::DEBTOR_WIDTH - $margin - 30;
        $y = $margin + $top + 7 + 56 + 1;
        $width = 30;
        $height = 10;
        $fpdf->SetDrawColor(0,0,0);
        $fpdf->SetLineWidth(0.25);
        $fpdf->Line($x, $y, $x + 2, $y);
        $fpdf->Line($x, $y, $x, $y + 1);

        $fpdf->Line($x, $y + $height, $x + 2, $y + $height);
        $fpdf->Line($x, $y + $height - 1, $x, $y + $height);

        $fpdf->Line($x + $width - 2, $y, $x + $width, $y);
        $fpdf->Line($x + $width, $y, $x + $width, $y + 1);

        $fpdf->Line($x + $width - 2, $y + $height, $x + $width, $y + $height);
        $fpdf->Line($x + $width, $y + $height - 1, $x + $width, $y + $height);

        if ($this->getConfiguration()->border)
            $fpdf->SetDrawColor(255,0,0);

        // Acceptance point
        $fpdf->SetFont('Arial', 'B', 6);
        $fpdf->SetXY($left, $top + $margin + 7 + 56 + 14);
        $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 5, 'Acceptance point', 0, 0, 'R');
    }

    private function printPaymentPart(Facture $facture, \FPDF $fpdf) {

        $top = self::A4_HEIGHT - self::PART_HEIGHT;
        $margin = self::PART_MARGIN;
        $left = self::DEBTOR_WIDTH + $margin;

        $fpdf->SetDrawColor(0,0,0);
        $fpdf->SetXY(self::DEBTOR_WIDTH, $top);
        $fpdf->Cell(self::PAYMENT_WIDTH, self::PART_HEIGHT, '', $this->getConfiguration()->border);

        $compte = $facture->getCompteToUse();
        $debiteur = $facture->getDebiteur();
        $adresse = $debiteur->getSendableAdresse();

        if ($this->getConfiguration()->border) {
            $fpdf->SetDrawColor(255,0,0);
            $fpdf->SetXY($left, $top + $margin);
            $fpdf->Cell(51, 7, '', 1);

            $fpdf->SetXY($left, $top + 2*$margin + 7);
            $fpdf->Cell(46, 46, '', 1);

            $fpdf->SetXY($left, $top + 7 + 3*$margin + 46);
            $fpdf->Cell(51, 22, '', 1);

            $fpdf->SetXY($left + 51, $top + $margin);
            $fpdf->Cell(87, 85, '', 1);

            $fpdf->SetXY($left, 7 + 56 + 22 + $top + $margin);
            $fpdf->Cell(148 - 2*$margin, 10, '', 1);
        }

        $fpdf->SetXY($left, $top + $margin);
        $fpdf->SetFont('Arial', 'B', 11);
        $fpdf->Cell(self::DEBTOR_WIDTH - 2*$margin, 7, 'Payment part');

        // Print qr
        $fpdf->Image($this->getQrCode($facture), $left, $top + 2*$margin + 7, 46, 46, 'png');

        // Montant
        $fpdf->SetXY($left, $top + 3*$margin + 7 + 46);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(14, 5, 'Currency');
        $fpdf->SetXY($left + 14, $top + 3*$margin + 7 + 46);
        $fpdf->Cell(10, 5, 'Amount');

        $fpdf->SetXY($left, $top + 3*$margin + 7 + 46 + 5);
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->Cell(12, 5, 'CHF');

        // draw user put amount
        $width = 40;
        $height = 15;
        $x = $left + 11;
        $y = $top + 3*$margin + 7 + 46 + 6;
        $fpdf->SetDrawColor(0,0,0);
        $fpdf->SetLineWidth(0.25);
        $fpdf->Line($x, $y, $x + 2, $y);
        $fpdf->Line($x, $y, $x, $y + 1);

        $fpdf->Line($x, $y + $height, $x + 2, $y + $height);
        $fpdf->Line($x, $y + $height - 1, $x, $y + $height);

        $fpdf->Line($x + $width - 2, $y, $x + $width, $y);
        $fpdf->Line($x + $width, $y, $x + $width, $y + 1);

        $fpdf->Line($x + $width - 2, $y + $height, $x + $width, $y + $height);
        $fpdf->Line($x + $width, $y + $height - 1, $x + $width, $y + $height);

        if ($this->getConfiguration()->border)
            $fpdf->SetDrawColor(255,0,0);

        // More information
        $sleft = $left + 51;
        $fpdf->SetXY($sleft, $top + $margin);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(14, 5, 'Account / Payable to');

        // address
        $fpdf->SetXY($sleft, $top + $margin + 5);
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->MultiCell(87, 5, implode("\n", [
            'CH56 1234 5678 0034 5453 5', //utf8_decode($compte->getIban()),
            utf8_decode($compte->getLine1()),
            utf8_decode($compte->getLine2()),
            utf8_decode($compte->getLine3()),
        ]));

        // Référence
        $fpdf->SetXY($sleft, $top + $margin + 25);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(87, 11, 'Reference');
        $fpdf->SetXY($sleft, $top + $margin + 33);
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->Cell(87, 4, '12 23456 67890 54546 67675 53689');

        // Informations additionnelles
        $fpdf->SetXY($sleft, $top + $margin + 36);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(87, 11, 'Additional information');
        $fpdf->SetXY($sleft, $top + $margin + 44);
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->Cell(87, 4, utf8_decode("Facture n. " . $facture->getFactureId()));

        // Payable by
        $fpdf->SetXY($sleft, $top + $margin + 48);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(87, 11, 'Payable by');

        // address
        $fpdf->SetXY($sleft, $top + $margin + 56);
        $fpdf->SetFont('Arial', '', 10);
        $fpdf->MultiCell(87, 5, implode("\n", [
            utf8_decode($debiteur->__toString()),
            utf8_decode($adresse->getRue()),
            utf8_decode($adresse->getNpa() . " " . $adresse->getLocalite()),
        ]));
    }

    private function getQrCode(Facture $facture) {

        $adresse = $facture->getDebiteur()->getSendableAdresse();
        $qrBill = QrBill::create();
        $qrBill->setCreditor(CombinedAddress::create(
            'Brigade de Sauvabelin',
            'Case postale 5455',
            '1002 Lausanne',
            'CH'
        ));

        $qrBill->setCreditorInformation(CreditorInformation::create('CH4431999123000889012'));

        $qrBill->setUltimateDebtor(CombinedAddress::create(
            $facture->getDebiteur()->__toString(),
            $adresse->getRue(),
            $adresse->getNpa() . ' ' . $adresse->getLocalite(),
            'CH'
        ));

        $montant = $facture->getMontantEncoreDu();
        $montant = $montant < 0 ? 0 : $montant;
        $qrBill->setPaymentAmountInformation(PaymentAmountInformation::create('CHF', $montant));
        $qrBill->setPaymentReference(PaymentReference::create(
            PaymentReference::TYPE_QR,
            QrPaymentReferenceGenerator::generate(
                '123456',
                $facture->getFactureId()
            )
        ));

        try {
            return 'data://text/plain;base64,' . base64_encode($qrBill->getQrCode()->writeString());
        } catch (\Exception $e) {
            dump($qrBill->getViolations());
            throw $e;
        }
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

    /**
     * Returns the form used to configure the export
     * @return string
     */
    public function getConfigFormClass()
    {
        return QrFactureConfigType::class;
    }

    /**
     * Returns the configuration object class
     * @return string
     */
    public function getConfigClass()
    {
        return QrFactureConfig::class;
    }
}
