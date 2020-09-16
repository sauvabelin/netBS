<?php

namespace TDGLBundle\Searcher;

use NetBS\FichierBundle\Searcher\MembreSearcher;
use TDGLBundle\Form\TDGLMembreSearchType;
use TDGLBundle\Model\TDGLMembreSearch;

class TDGLMembreSearcher extends MembreSearcher
{
    public function getSearchType()
    {
        return TDGLMembreSearchType::class;
    }

    public function getSearchObject()
    {
        return new TDGLMembreSearch();
    }

    /**
     * Returns the twig template used to render the form. A variable casually named 'form' will be available
     * for you to use
     * @return string
     */
    public function getFormTemplate()
    {
        return '@TDGL/membre/search_membre.html.twig';
    }
}
