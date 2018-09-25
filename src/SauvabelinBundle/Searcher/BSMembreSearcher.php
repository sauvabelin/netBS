<?php

namespace SauvabelinBundle\Searcher;

use NetBS\FichierBundle\Searcher\MembreSearcher;
use SauvabelinBundle\Form\Search\SearchBaseMembreInformationType;
use SauvabelinBundle\Model\SearchMembre;

class BSMembreSearcher extends MembreSearcher
{
    /**
     * Returns the search form type class
     * @return string
     */
    public function getSearchType()
    {
        return SearchBaseMembreInformationType::class;
    }

    /**
     * Returns the twig template used to render the form. A variable casually named 'form' will be available
     * for you to use
     * @return string
     */
    public function getFormTemplate()
    {
        return '@Sauvabelin/membre/search_membre.html.twig';
    }

    /**
     * Returns an object used to render form, which will contain search data
     * @return object
     */
    public function getSearchObject()
    {
        return new SearchMembre();
    }

}