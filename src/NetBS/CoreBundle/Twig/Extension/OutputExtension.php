<?php

namespace NetBS\CoreBundle\Twig\Extension;

class OutputExtension extends \Twig_Extension
{
    public function getFunctions() {

        return [

            new \Twig_SimpleFunction('fake_row', [$this, 'fakeRowFunction'], array('is_safe' => array('html')))
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
}
