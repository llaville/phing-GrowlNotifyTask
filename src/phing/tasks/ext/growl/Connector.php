<?php
require_once "phing/types/DataType.php";
require_once 'phing/tasks/ext/growl/ConnectorOptions.php';

/**
 * This type represents a generic Net_Growl connector
 */
class Connector extends DataType
{
    private $options;
    private $type;

    public function __construct()
    {
        $this->createOptions();
    }
    
    /**
     * Ensures that there are no circular references
     * and that the reference is of the correct type.
     *
     * @return Connector
     */
    public function getRef(Project $p)
    {
        if ( !$this->checked ) {
            $stk = array();
            array_push($stk, $this);
            $this->dieOnCircularReference($stk, $p);
        }
        $o = $this->ref->getReferencedObject($p);
        if ( !($o instanceof Connector) ) {
            throw new BuildException(
                $this->ref->getRefId()." does not denote a Net_Growl Connector"
            );
        }
        return $o;
    }

    public function getType()
    {
        if ($this->isReference()) {
            $p = $this->getProject();
            return $this->getRef($p)->getType();
        }

        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function createOptions()
    {
        return ($this->options = new ConnectorOptions());
    }

    /**
     * Gets a combined hash/array options for a Net_Growl connector.
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->isReference()) {
            $p = $this->getProject();
            return $this->getRef($p)->getOptions();
        }

        return $this->options->toArray();
    }

}
