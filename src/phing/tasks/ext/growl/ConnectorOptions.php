<?php

require_once 'phing/tasks/ext/growl/ConnectorOption.php';

class ConnectorOptions
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @return ConnectorOption
     */
    public function createOption()
    {
        return ($this->options[] = new ConnectorOption());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $metadata = array();

        foreach ($this->options as $option) {
            $metadata[$option->getName()] = $option->toArray();
        }

        return $metadata;
    }

}
