<?php

namespace NetBS\CoreBundle\Model;

use NetBS\CoreBundle\Service\ParameterManager;
use NetBS\CoreBundle\Service\QueryMaker;
use NetBS\ListBundle\Model\BaseListModel;
use Symfony\Component\Form\Form;

abstract class BaseSearcher extends BaseListModel
{
    /**
     * @var QueryMaker
     */
    protected $queryMaker;

    /**
     * @var ParameterManager
     */
    protected $parameterManager;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var null|array
     */
    protected $results  = null;

    /**
     * Returns the search form type class
     * @return string
     */
    abstract public function getSearchType();

    /**
     * Returns an object used to render form, which will contain search data
     * @return object
     */
    abstract public function getSearchObject();

    /**
     * Returns the twig template used to render the form. A variable casually named 'form' will be available
     * for you to use
     * @return string
     */
    abstract public function getFormTemplate();

    public function setQueryMaker(QueryMaker $queryMaker) {

        $this->queryMaker   = $queryMaker;
    }

    public function setParameterManager(ParameterManager $manager) {

        $this->parameterManager = $manager;
    }

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        $limit      = $this->getMaxResults();
        $results    = $this->getResults();
        return count($results) > intval($limit) ? [] : $results;
    }

    public function getResults() {

        if($this->results !== null)
            return $this->results;

        $form   = $this->form;

        if(!$form)
            return [];

        $query = $this->queryMaker->buildQuery($this->getManagedItemsClass(), $form);

        $this->results = $query->getQuery()->execute();
        return $this->results;
    }

    protected function getMaxResults() {

        return intval($this->parameterManager->getValue('search', 'max_results'));
    }

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias()
    {
        return "netbs.searcher";
    }

    public function setForm(Form $form) {

        $this->form = $form;
    }
}