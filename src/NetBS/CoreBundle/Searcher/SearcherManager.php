<?php

namespace NetBS\CoreBundle\Searcher;

use NetBS\CoreBundle\Model\BaseSearcher;
use NetBS\CoreBundle\Model\SearchInstance;
use NetBS\CoreBundle\Service\QueryMaker;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SearcherManager
{
    /**
     * @var BaseSearcher[]
     */
    protected $searcbers = [];

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var FormFactory
     */
    protected $factory;

    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var QueryMaker
     */
    protected $queryMaker;

    public function __construct(RequestStack $requestStack, \Twig_Environment $twig, FormFactory $factory, QueryMaker $queryMaker) {

        $this->twig         = $twig;
        $this->queryMaker   = $queryMaker;
        $this->factory      = $factory;
        $this->request      = $requestStack->getCurrentRequest();
    }

    /**
     * @param BaseSearcher $searcher
     */
    public function registerSearcher(BaseSearcher $searcher) {
        $this->searcbers[$searcher->getManagedItemsClass()] = $searcher;
    }

    /**
     * @param $class
     * @return BaseSearcher
     * @throws \Exception
     */
    public function getSearcher($class) {

        if(!isset($this->searcbers[$class]))
            throw new \Exception("No searcher found for class $class");

        return $this->searcbers[$class];
    }

    /**
     * @param string $class
     * @return SearchInstance
     */
    public function bind($class) {

        $searcher   = $this->getSearcher($class);
        $form       = $this->factory->create($searcher->getSearchType(), $searcher->getSearchObject());

        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid())
            $searcher->setForm($form);

        return new SearchInstance($searcher, $form);
    }

    /**
     * @param $searcher
     * @param $form
     * @return SearchInstance
     */
    public function bindForm($searcher, $form) {

        return new SearchInstance($searcher, $form);
    }

    public function render(SearchInstance $instance, array $params = []) {

        $form   = $instance->getForm();

        return new Response($this->twig->render($instance->getSearcher()->getFormTemplate(), array_merge($params, [
            'form'          => $form->createView(),
            'searcher'      => $instance->getSearcher(),
        ])));
    }
}