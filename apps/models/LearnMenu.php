<?php

namespace Learncom\Models;

class LearnMenu extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $menu_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $menu_type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $menu_name;
    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $menu_excluded;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $menu_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $menu_order;

    /**
     * Method to set the value of field menu_id
     *
     * @param integer $menu_id
     * @return $this
     */
    public function setMenuId($menu_id)
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    /**
     * Method to set the value of field menu_type
     *
     * @param string $menu_type
     * @return $this
     */
    public function setMenuType($menu_type)
    {
        $this->menu_type = $menu_type;

        return $this;
    }

    /**
     * Method to set the value of field menu_name
     *
     * @param string $menu_name
     * @return $this
     */
    public function setMenuName($menu_name)
    {
        $this->menu_name = $menu_name;

        return $this;
    }

    /**
     * Method to set the value of field menu_excluded
     *
     * @param string $menu_excluded
     * @return $this
     */
    public function setMenuExcluded($menu_excluded)
    {
        $this->menu_excluded = $menu_excluded;

        return $this;
    }
    /**
     * Method to set the value of field menu_active
     *
     * @param integer $menu_active
     * @return $this
     */
    public function setMenuActive($menu_active)
    {
        $this->menu_active = $menu_active;

        return $this;
    }

    /**
     * Method to set the value of field menu_order
     *
     * @param integer $menu_order
     * @return $this
     */
    public function setMenuOrder($menu_order)
    {
        $this->menu_order = $menu_order;

        return $this;
    }

    /**
     * Returns the value of field menu_id
     *
     * @return integer
     */
    public function getMenuId()
    {
        return $this->menu_id;
    }

    /**
     * Returns the value of field menu_type
     *
     * @return string
     */
    public function getMenuType()
    {
        return $this->menu_type;
    }

    /**
     * Returns the value of field menu_name
     *
     * @return string
     */
    public function getMenuName()
    {
        return $this->menu_name;
    }
    /**
     * Returns the value of field menu_excluded
     *
     * @return string
     */
    public function getMenuExcluded()
    {
        return $this->menu_excluded;
    }
    /**
     * Returns the value of field menu_active
     *
     * @return integer
     */
    public function getMenuActive()
    {
        return $this->menu_active;
    }

    /**
     * Returns the value of field menu_order
     *
     * @return integer
     */
    public function getMenuOrder()
    {
        return $this->menu_order;
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
        return 'learn_menu';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnMenu[]|LearnMenu
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnMenu
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
