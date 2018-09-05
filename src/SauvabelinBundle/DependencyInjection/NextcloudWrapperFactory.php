<?php

namespace SauvabelinBundle\DependencyInjection;

use NextcloudApiWrapper\Wrapper;

class NextcloudWrapperFactory
{
    public static function createWrapper($basePath, $username, $password) {

        return Wrapper::build($basePath, $username, $password);
    }
}