<?php

namespace NetBS\ListBundle\Model;

use NetBS\ListBundle\Column\BaseColumn;

class ListColumnsConfiguration
{
    protected $columns = [];

    /**
     * Adds a column to the list
     * @param string            $header     Column header
     * @param string|\Closure   $accessor   Targeted property, string => propertyAccessor, closure performed on item
     * @param string            $class      The column class
     * @param array             $params     Some params required by the column
     *
     * @return $this
     */
    public function addColumn($header, $accessor, $class, array $params = []) {

        $this->columns[] = [

            'header'    => $header,
            'accessor'  => $accessor,
            'class'     => $class,
            'params'    => $params
        ];

        return $this;
    }

    /**
     * @return BaseColumn[]
     */
    public function getColumns() {

        return $this->columns;
    }
}