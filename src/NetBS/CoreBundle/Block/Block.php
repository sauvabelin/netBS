<?php

namespace NetBS\CoreBundle\Block;

class Block extends MetaBlock
{
    protected $content;

    public function __construct($content, $class, array $params = [])
    {
        parent::__construct($class, $params);
        $this->content  = $content;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}