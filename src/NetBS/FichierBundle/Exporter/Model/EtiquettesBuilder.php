<?php

namespace NetBS\FichierBundle\Exporter\Model;

use NetBS\FichierBundle\Exporter\Config\EtiquettesConfig;

class EtiquettesBuilder extends \FPDF
{
    private $config;

    private $countX = 0;

    private $countY = 0;

    private $etiquetteWidth;

    private $etiquetteHeight;

    private $init   = false;

    public function __construct(EtiquettesConfig $config)
    {
        parent::__construct('P', 'mm', 'A4');

        $this->config   = $config;
        $this->SetFont('Arial');
        $this->SetFontSize($config->taillePolice);
        $this->SetMargins(0,0);
        $this->SetAutoPageBreak(false);

        $this->etiquetteWidth   = ($this->w
            - ($this->config->FPDFConfig->margeGauche + $this->config->margeDroite))
            / $this->config->colonnes;

        $this->etiquetteHeight  = ($this->h
            - ($this->config->FPDFConfig->margeHaut + $this->config->margeInferieure))
            / $this->config->lignes;
    }

    public function addEtiquette(array $rows) {

        if(!$this->init)
            $this->init = true;
        else
            $this->updateCount();

        $posX   = $this->config->FPDFConfig->margeGauche
            + $this->countX*$this->etiquetteWidth + ($this->config->margeInterieureHorizontale/2);

        $posY   = $this->config->FPDFConfig->margeHaut
            + $this->countY*$this->etiquetteHeight + ($this->config->margeInterieureVerticale/2);

        $label  = utf8_decode(implode("\n", $rows));

        $this->SetXY($posX, $posY);
        $this->MultiCell(
            $this->etiquetteWidth - ($this->config->margeInterieureHorizontale),
            $this->config->FPDFConfig->interligne,
            $label,
            0,
            'L'
        );
    }

    private function updateCount() {

        if($this->countX == $this->config->colonnes-1) {

            $this->countX      = 0;

            if($this->countY == $this->config->lignes-1) {

                $this->countY   = 0;
                $this->AddPage();
            }

            else
                $this->countY++;
        }

        else
            $this->countX++;
    }
}