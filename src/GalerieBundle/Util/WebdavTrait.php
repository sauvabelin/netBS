<?php

namespace GalerieBundle\Util;

trait WebdavTrait
{
    public function getsearchPath() {

        $basePath   = $this->getWebdavUrl();
        return substr($basePath, 6);
    }
}