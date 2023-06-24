<?php

namespace Learncom\Models;

class LearnBanner extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $banner_id;
    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $banner_type;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $banner_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $banner_content;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $banner_link;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $banner_image;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $banner_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $banner_active;

    /**
     * Method to set the value of field banner_id
     *
     * @param integer $banner_id
     * @return $this
     */
    public function setBannerId($banner_id)
    {
        $this->banner_id = $banner_id;

        return $this;
    }
    /**
     * Method to set the value of field banner_type
     *
     * @param string $banner_type
     * @return $this
     */
    public function setBannerType($banner_type)
    {
        $this->banner_type = $banner_type;

        return $this;
    }
    /**
     * Method to set the value of field banner_name
     *
     * @param string $banner_name
     * @return $this
     */
    public function setBannerName($banner_name)
    {
        $this->banner_name = $banner_name;

        return $this;
    }

    /**
     * Method to set the value of field banner_content
     *
     * @param string $banner_content
     * @return $this
     */
    public function setBannerContent($banner_content)
    {
        $this->banner_content = $banner_content;

        return $this;
    }

    /**
     * Method to set the value of field banner_link
     *
     * @param string $banner_link
     * @return $this
     */
    public function setBannerLink($banner_link)
    {
        $this->banner_link = $banner_link;

        return $this;
    }

    /**
     * Method to set the value of field banner_image
     *
     * @param string $banner_image
     * @return $this
     */
    public function setBannerImage($banner_image)
    {
        $this->banner_image = $banner_image;

        return $this;
    }

    /**
     * Method to set the value of field banner_order
     *
     * @param integer $banner_order
     * @return $this
     */
    public function setBannerOrder($banner_order)
    {
        $this->banner_order = $banner_order;

        return $this;
    }

    /**
     * Method to set the value of field banner_active
     *
     * @param string $banner_active
     * @return $this
     */
    public function setBannerActive($banner_active)
    {
        $this->banner_active = $banner_active;

        return $this;
    }

    /**
     * Returns the value of field banner_id
     *
     * @return integer
     */
    public function getBannerId()
    {
        return $this->banner_id;
    }
    /**
     * Returns the value of field banner_type
     *
     * @return string
     */
    public function getBannerType()
    {
        return $this->banner_type;
    }
    /**
     * Returns the value of field banner_name
     *
     * @return string
     */
    public function getBannerName()
    {
        return $this->banner_name;
    }

    /**
     * Returns the value of field banner_content
     *
     * @return string
     */
    public function getBannerContent()
    {
        return $this->banner_content;
    }

    /**
     * Returns the value of field banner_link
     *
     * @return string
     */
    public function getBannerLink()
    {
        return $this->banner_link;
    }

    /**
     * Returns the value of field banner_image
     *
     * @return string
     */
    public function getBannerImage()
    {
        return $this->banner_image;
    }

    /**
     * Returns the value of field banner_order
     *
     * @return integer
     */
    public function getBannerOrder()
    {
        return $this->banner_order;
    }

    /**
     * Returns the value of field banner_active
     *
     * @return string
     */
    public function getBannerActive()
    {
        return $this->banner_active;
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
        return 'learn_banner';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnBanner[]|LearnBanner
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnBanner
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
