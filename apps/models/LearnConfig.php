<?php

namespace Learncom\Models;

class LearnConfig extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=255, nullable=false)
     */
    protected $config_key;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $config_content;

    /**
     * Method to set the value of field config_key
     *
     * @param string $config_key
     * @return $this
     */
    public function setConfigKey($config_key)
    {
        $this->config_key = $config_key;

        return $this;
    }

    /**
     * Method to set the value of field config_content
     *
     * @param string $config_content
     * @return $this
     */
    public function setConfigContent($config_content)
    {
        $this->config_content = $config_content;

        return $this;
    }

    /**
     * Returns the value of field config_key
     *
     * @return string
     */
    public function getConfigKey()
    {
        return $this->config_key;
    }

    /**
     * Returns the value of field config_content
     *
     * @return string
     */
    public function getConfigContent()
    {
        return $this->config_content;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("hoc_truc_tuyen");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'learn_config';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnConfig[]|LearnConfig
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnConfig
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
