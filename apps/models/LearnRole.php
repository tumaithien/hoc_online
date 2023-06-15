<?php

namespace Learncom\Models;

class LearnRole extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $role_id;

    /**
     *
     * @var string
     * @Column(type="string", length=256, nullable=false)
     */
    protected $role_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $role_actions;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $role_active;

    /**
     * Method to set the value of field role_id
     *
     * @param integer $role_id
     * @return $this
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;

        return $this;
    }

    /**
     * Method to set the value of field role_name
     *
     * @param string $role_name
     * @return $this
     */
    public function setRoleName($role_name)
    {
        $this->role_name = $role_name;

        return $this;
    }

    /**
     * Method to set the value of field role_actions
     *
     * @param string $role_actions
     * @return $this
     */
    public function setRoleActions($role_actions)
    {
        $this->role_actions = $role_actions;

        return $this;
    }

    /**
     * Method to set the value of field role_active
     *
     * @param string $role_active
     * @return $this
     */
    public function setRoleActive($role_active)
    {
        $this->role_active = $role_active;

        return $this;
    }

    /**
     * Returns the value of field role_id
     *
     * @return integer
     */
    public function getRoleId()
    {
        return $this->role_id;
    }

    /**
     * Returns the value of field role_name
     *
     * @return string
     */
    public function getRoleName()
    {
        return $this->role_name;
    }

    /**
     * Returns the value of field role_actions
     *
     * @return string
     */
    public function getRoleActions()
    {
        return $this->role_actions;
    }

    /**
     * Returns the value of field role_active
     *
     * @return string
     */
    public function getRoleActive()
    {
        return $this->role_active;
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
        return 'learn_role';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnRole[]|LearnRole
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnRole
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstById($id)
    {
        return self::findFirst(array(
            "role_id =:ID:",
            'bind' => array('ID' => $id)
        ));
    }

}
