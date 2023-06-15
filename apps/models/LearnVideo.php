<?php

namespace Learncom\Models;

class LearnVideo extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $video_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $video_type;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $video_link;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $video_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $video_class_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $video_subject_id;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $video_chapter_id;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $video_group_id;
    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $video_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $video_order;

    /**
     * Method to set the value of field video_id
     *
     * @param integer $video_id
     * @return $this
     */
    public function setVideoId($video_id)
    {
        $this->video_id = $video_id;

        return $this;
    }
    /**
     * Method to set the value of field video_type
     *
     * @param string $video_type
     * @return $this
     */
    public function setVideoType($video_type)
    {
        $this->video_type = $video_type;

        return $this;
    }
    /**
     * Method to set the value of field video_name
     *
     * @param string $video_name
     * @return $this
     */
    public function setVideoName($video_name)
    {
        $this->video_name = $video_name;

        return $this;
    }

    /**
     * Method to set the value of field video_link
     *
     * @param string $video_link
     * @return $this
     */
    public function setVideoLink($video_link)
    {
        $this->video_link = $video_link;

        return $this;
    }

    /**
     * Method to set the value of field video_class_id
     *
     * @param integer $video_class_id
     * @return $this
     */
    public function setVideoClassId($video_class_id)
    {
        $this->video_class_id = $video_class_id;

        return $this;
    }

    /**
     * Method to set the value of field video_subject_id
     *
     * @param integer $video_subject_id
     * @return $this
     */
    public function setVideoSubjectId($video_subject_id)
    {
        $this->video_subject_id = $video_subject_id;

        return $this;
    }
    /**
     * Method to set the value of field video_chapter_id
     *
     * @param integer $video_chapter_id
     * @return $this
     */
    public function setVideoChapterId($video_chapter_id)
    {
        $this->video_chapter_id = $video_chapter_id;

        return $this;
    }
    /**
     * Method to set the value of field video_group_id
     *
     * @param integer $video_group_id
     * @return $this
     */
    public function setVideoGroupId($video_group_id)
    {
        $this->video_group_id = $video_group_id;

        return $this;
    }
    /**
     * Method to set the value of field video_active
     *
     * @param string $video_active
     * @return $this
     */
    public function setVideoActive($video_active)
    {
        $this->video_active = $video_active;

        return $this;
    }

    /**
     * Method to set the value of field video_order
     *
     * @param integer $video_order
     * @return $this
     */
    public function setVideoOrder($video_order)
    {
        $this->video_order = $video_order;

        return $this;
    }

    /**
     * Returns the value of field video_id
     *
     * @return integer
     */
    public function getVideoId()
    {
        return $this->video_id;
    }
    /**
     * Returns the value of field video_type
     *
     * @return string
     */
    public function getVideoType()
    {
        return $this->video_type;
    }
    /**
     * Returns the value of field video_name
     *
     * @return string
     */
    public function getVideoName()
    {
        return $this->video_name;
    }

    /**
     * Returns the value of field video_link
     *
     * @return string
     */
    public function getVideoLink()
    {
        return $this->video_link;
    }

    /**
     * Returns the value of field video_class_id
     *
     * @return integer
     */
    public function getVideoClassId()
    {
        return $this->video_class_id;
    }

    /**
     * Returns the value of field video_subject_id
     *
     * @return integer
     */
    public function getVideoSubjectId()
    {
        return $this->video_subject_id;
    }
    /**
     * Returns the value of field video_chapter_id
     *
     * @return integer
     */
    public function getVideoChapterId()
    {
        return $this->video_chapter_id;
    }
    /**
     * Returns the value of field video_group_id
     *
     * @return integer
     */
    public function getVideoGroupId()
    {
        return $this->video_group_id;
    }
    /**
     * Returns the value of field video_active
     *
     * @return string
     */
    public function getVideoActive()
    {
        return $this->video_active;
    }

    /**
     * Returns the value of field video_order
     *
     * @return integer
     */
    public function getVideoOrder()
    {
        return $this->video_order;
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
        return 'learn_video';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnVideo[]|LearnVideo
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnVideo
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
