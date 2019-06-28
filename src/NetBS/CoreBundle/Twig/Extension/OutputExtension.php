<?php

namespace NetBS\CoreBundle\Twig\Extension;

use NetBS\CoreBundle\Service\LoaderManager;

class OutputExtension extends \Twig_Extension
{
    private $loaders;

    public function __construct(LoaderManager $loaderManager)
    {
        $this->loaders = $loaderManager;
    }

    public function getFunctions() {

        return [

            new \Twig_SimpleFunction('fake_row', [$this, 'fakeRowFunction'], array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('loader_id', [$this, 'loadedId'])
        ];
    }

    public function getFilters() {

        return [

            new \Twig_SimpleFilter('bool', [$this, 'formatBool'])
        ];
    }

    public function fakeRowFunction($label, $value) {

        return "<div class=\"form-group row\" style=\"padding:3px 0;\">
        <label class=\"col-lg-5 control-label\" style=\"padding-top:6px;text-align:left;\">$label</label>
        <div class=\"col-lg-7\"><span style='line-height:30px;'>$value</span></div></div>";
    }

    public function formatBool($value) {

        return boolval($value) ? 'Oui' : 'Non';
    }

    public function loadedId($item, $class = null) {

        if ($class === null) $class = get_class($item);

        if ($this->loaders->hasLoader($class))
            return $this->loaders->getLoader($class)->toId($item);
        return $item->getId();
    }
}
