<?php

namespace NetBS\SecureBundle\Service;

use NetBS\SecureBundle\Mapping\BaseUser;
use NetBS\SecureBundle\Mapping\BaseRole;

class SecureConfig
{
    /**
     * @var array
     */
    protected $config;

    public function setConfig(array $config)
    {
        if(!is_subclass_of($config['entities']['user_class'], BaseUser::class))
            throw new \Exception("redefined user class must extend " . BaseUser::class);

        if(!is_subclass_of($config['entities']['role_class'], BaseRole::class))
            throw new \Exception("redefined role class must extend " . BaseRole::class);

        $this->config   = $config;
    }

    public function getUserClass() {
        return $this->config['entities']['user_class'];
    }

    public function getRoleClass() {
        return $this->config['entities']['role_class'];
    }

    /**
     * @return BaseUser
     */
    public function createUser() {

        $class  = $this->getUserClass();
        return new $class();
    }
}