<?php

namespace NetBS\CoreBundle\Model;

use NetBS\ListBundle\Model\BaseListModel;

abstract class BaseAutomatic extends BaseListModel
{
    protected $_data    = null;

    /**
     * @return string
     * Returns this list's name, displayed
     */
    abstract public function getName();

    /**
     * @return string
     * Returns this list's description, displayed
     */
    abstract public function getDescription();

    /**
     * @param $data
     * @return array
     */
    abstract protected function getItems($data = null);

    /**
     * Retrieves all elements managed by this list
     * @return array
     */
    protected function buildItemsList()
    {
        return $this->getItems($this->_data);
    }

    public function _setAutomaticData($data) {
        $this->_data    = $data;
    }
}