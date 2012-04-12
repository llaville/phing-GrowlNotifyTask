<?php

class ConnectorOption extends ConnectorOptions
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|array
     */
    public function getValue()
    {
        return (empty($this->options) ? $this->value : $this->options);
    }

    /**
     * @return string|array
     */
    public function toArray()
    {
        return (empty($this->options) ? $this->value : parent::toArray());
    }
}
