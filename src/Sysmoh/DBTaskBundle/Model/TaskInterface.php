<?php

namespace Sysmoh\DBTaskBundle\Model;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface TaskInterface
{
    /**
     * Runs the task with the given parameters
     * @param array $params
     * @return boolean
     */
    public function run(array $params);

    /**
     * Defines this tasks required and default params
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Returns a unique name for the given task
     * @return string
     */
    public function getName();
}