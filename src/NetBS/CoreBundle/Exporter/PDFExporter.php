<?php

namespace NetBS\CoreBundle\Exporter;

use Knp\Snappy\GeneratorInterface;
use NetBS\CoreBundle\Model\ExporterInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class PDFExporter implements ExporterInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var GeneratorInterface
     */
    protected $snappy;

    abstract public function renderView($items);

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig) {

        $this->twig = $twig;
    }

    /**
     * @param GeneratorInterface $snappy
     */
    public function setSnappy($snappy) {

        $this->snappy   = $snappy;
    }

    /**
     * Returns the exported type, like "excel", "pdf" or something..
     * @return string
     */
    public function getCategory()
    {
        return 'pdf';
    }

    /**
     * Returns an exported representation of given items
     * @param \Traversable $items
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export($items)
    {
        return new Response(
            $this->snappy->getOutputFromHtml($this->renderView($items)),
            200,
            array(
                'Content-Type'  => 'application/pdf'
            )
        );
    }
}