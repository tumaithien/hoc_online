<?php

namespace Learncom\Models;

class LearnDocument extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $document_id;
    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $document_type;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $document_name;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $document_group_ids;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $document_link;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $document_class_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $document_subject_id;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $document_chapter_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $document_active;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $document_is_public;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $document_order;

    /**
     * Method to set the value of field document_id
     *
     * @param integer $document_id
     * @return $this
     */
    public function setDocumentId($document_id)
    {
        $this->document_id = $document_id;

        return $this;
    }
    /**
     * Method to set the value of field document_type
     *
     * @param string $document_type
     * @return $this
     */
    public function setDocumentType($document_type)
    {
        $this->document_type = $document_type;

        return $this;
    }
    /**
     * Method to set the value of field document_name
     *
     * @param string $document_name
     * @return $this
     */
    public function setDocumentName($document_name)
    {
        $this->document_name = $document_name;

        return $this;
    }
    /**
     * Method to set the value of field document_group_ids
     *
     * @param string $document_group_ids
     * @return $this
     */
    public function setDocumentGroupIds($document_group_ids)
    {
        $this->document_group_ids = $document_group_ids;

        return $this;
    }
    /**
     * Method to set the value of field document_link
     *
     * @param string $document_link
     * @return $this
     */
    public function setDocumentLink($document_link)
    {
        $this->document_link = $document_link;

        return $this;
    }

    /**
     * Method to set the value of field document_class_id
     *
     * @param integer $document_class_id
     * @return $this
     */
    public function setDocumentClassId($document_class_id)
    {
        $this->document_class_id = $document_class_id;

        return $this;
    }

    /**
     * Method to set the value of field document_subject_id
     *
     * @param integer $document_subject_id
     * @return $this
     */
    public function setDocumentSubjectId($document_subject_id)
    {
        $this->document_subject_id = $document_subject_id;

        return $this;
    }
    /**
     * Method to set the value of field document_chapter_id
     *
     * @param integer $document_chapter_id
     * @return $this
     */
    public function setDocumentChapterId($document_chapter_id)
    {
        $this->document_chapter_id = $document_chapter_id;

        return $this;
    }

    /**
     * Method to set the value of field document_active
     *
     * @param string $document_active
     * @return $this
     */
    public function setDocumentActive($document_active)
    {
        $this->document_active = $document_active;

        return $this;
    }

    /**
     * Method to set the value of field document_is_public
     *
     * @param string $document_is_public
     * @return $this
     */
    public function setDocumentIsPublic($document_is_public)
    {
        $this->document_is_public = $document_is_public;

        return $this;
    }

    /**
     * Method to set the value of field document_order
     *
     * @param integer $document_order
     * @return $this
     */
    public function setDocumentOrder($document_order)
    {
        $this->document_order = $document_order;

        return $this;
    }

    /**
     * Returns the value of field document_id
     *
     * @return integer
     */
    public function getDocumentId()
    {
        return $this->document_id;
    }
    /**
     * Returns the value of field document_type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->document_type;
    }

    /**
     * Returns the value of field document_name
     *
     * @return string
     */
    public function getDocumentName()
    {
        return $this->document_name;
    }
    /**
     * Returns the value of field document_group_ids
     *
     * @return string
     */
    public function getDocumentGroupIds()
    {
        return $this->document_group_ids;
    }
    /**
     * Returns the value of field document_link
     *
     * @return string
     */
    public function getDocumentLink()
    {
        return $this->document_link;
    }

    /**
     * Returns the value of field document_class_id
     *
     * @return integer
     */
    public function getDocumentClassId()
    {
        return $this->document_class_id;
    }

    /**
     * Returns the value of field document_subject_id
     *
     * @return integer
     */
    public function getDocumentSubjectId()
    {
        return $this->document_subject_id;
    }
    /**
     * Returns the value of field document_chapter_id
     *
     * @return integer
     */
    public function getDocumentChapterId()
    {
        return $this->document_chapter_id;
    }

    /**
     * Returns the value of field document_active
     *
     * @return string
     */
    public function getDocumentActive()
    {
        return $this->document_active;
    }

    /**
     * Returns the value of field document_is_public
     *
     * @return string
     */
    public function getDocumentIsPublic()
    {
        return $this->document_is_public;
    }

    /**
     * Returns the value of field document_order
     *
     * @return integer
     */
    public function getDocumentOrder()
    {
        return $this->document_order;
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
        return 'learn_document';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnDocument[]|LearnDocument
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnDocument
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
