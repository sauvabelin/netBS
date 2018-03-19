<?php

namespace NetBS\ListBundle\Model;

use NetBS\ListBundle\Model\ListColumnsConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Interface ListModelInterface
 * Implemented by all ListModels
 */
interface ListModelInterface
{
    /**
     * Returns the class of items managed by this list
     * @return string
     */
    public function getManagedItemsClass();

    /**
     * Returns this list's alias
     * @return string
     */
    public function getAlias();

    /**
     * Returns all elements managed by this list
     * @param bool $refresh If you implement some cache strategy, $refresh means "reload everything"
     * @return array
     */
    public function getElements($refresh = false);

    /**
     * Configures the list columns
     * @param ListColumnsConfiguration $configuration
     */
    public function configureColumns(ListColumnsConfiguration $configuration);

    /**
     * If this list requires additional parameters, configure them here
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Sets a parameter required by the list to work
     * @param string $key      the parameter key
     * @param mixed  $value
     */
    public function setParameter($key, $value);

    /**
     * Returns the parameter identified by the given key
     * @param string $key
     * @return mixed
     */
    public function getParameter($key);
}