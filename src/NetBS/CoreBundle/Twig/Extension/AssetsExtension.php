<?php

namespace NetBS\CoreBundle\Twig\Extension;

class AssetsExtension extends \Twig_Extension
{
    protected $styles   = [];
    protected $css      = [];
    protected $js       = [];
    protected $scripts  = [];
    protected $html     = [];
    protected $sorter   = null;

    public function __construct()
    {
        $this->sorter   = function(array $i1, array $i2) {
            if($i1['weight'] == $i2['weight'])
                return 0;

            return $i1['weight'] < $i2['weight'] ? -1 : 1;
        };
    }

    public function getFunctions() {

        return [

            new \Twig_SimpleFunction('registerStyle', array($this, 'registerStyle')),
            new \Twig_SimpleFunction('registerCss', array($this, 'registerCss')),
            new \Twig_SimpleFunction('registerJs', array($this, 'registerJs')),
            new \Twig_SimpleFunction('registerScript', array($this, 'registerScript')),
            new \Twig_SimpleFunction('registerHtml', array($this, 'registerHtml')),
            new \Twig_SimpleFunction('dumpStyle', array($this, 'dumpStyle'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dumpCss', array($this, 'dumpCss'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dumpJs', array($this, 'dumpJs'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dumpScript', array($this, 'dumpScript'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dumpHtml', array($this, 'dumpHtml'), array('is_safe' => array('html')))
        ];
    }

    public function registerStyle($style, $weight = 0) {

        foreach ($this->styles as $item)
            if($item['style'] === $style)
                return;

        $this->styles[] = ['style' => $style, 'weight' => $weight];
    }

    public function registerCss($css, $weight = 0) {

        foreach ($this->css as $style)
            if($style['css'] === $css)
                return;

        $this->css[] = ['css' => $css, 'weight' => $weight];
    }

    public function registerJs($js, $weight = 0) {

        foreach($this->js as $script)
            if($script['js'] === $js)
                return;

        $this->js[] = ['js' => $js, 'weight' => $weight];
    }

    public function registerScript($script, $weight = 0) {

        foreach($this->scripts as $scr)
            if($scr['script'] === $script)
                return;

        $this->scripts[]    = ['script' => $script, 'weight' => $weight];
    }

    public function registerHtml($html, $weight = 0) {

        foreach($this->html as $item)
            if($item['html'] == $html)
                return;

        $this->html[]    = ['html' => $html, 'weight' => $weight];
    }

    public function dumpStyle() {

        $style          = '';
        $styles         = $this->styles;
        usort($styles, $this->sorter);

        foreach($styles as $item)
            $style .= $item['style'];

        return $style;
    }


    public function dumpCss() {

        $css            = '';
        $stylesheets    = $this->css;
        usort($stylesheets, $this->sorter);

        foreach($stylesheets as $style)
            $css .= '<link rel="stylesheet" type="text/css" href="' . $style['css'] . '">' . "\n";

        return $css;
    }

    public function dumpJs() {

        $js         = '';
        $scripts    = $this->js;
        usort($scripts, $this->sorter);

        foreach($scripts as $script)
            $js .= '<script type="text/javascript" src="' . $script['js'] . '"></script>' . "\n";

        return $js;
    }

    public function dumpScript() {

        $scripting  = '';
        $scripts    = $this->scripts;
        usort($scripts, $this->sorter);
        foreach($scripts as $script)
            $scripting .= $script['script'];

        return $scripting;
    }

    public function dumpHtml() {

        $html       = '';
        $objects    = $this->html;
        usort($objects, $this->sorter);
        foreach($objects as $tags)
            $html .= $tags['html'];

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'assets';
    }
}
