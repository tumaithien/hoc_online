<?php

namespace Learncom\Models;

class LearnTestFolder extends BaseModelCache
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $folder_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $folder_test_id;
    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $folder_status;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $folder_link;
    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $folder_link_comment;
    /**
     *
     * @var string
     * @Column(type="string", length=10, nullable=true)
     */
    protected $folder_color;



    /**
     * Method to set the value of field folder_id
     *
     * @param integer $folder_id
     * @return $this
     */
    public function setFolderId($folder_id)
    {
        $this->folder_id = $folder_id;

        return $this;
    }

    /**
     * Method to set the value of field folder_test_id
     *
     * @param integer $folder_test_id
     * @return $this
     */
    public function setFolderTestId($folder_test_id)
    {
        $this->folder_test_id = $folder_test_id;

        return $this;
    }
    /**
     * Method to set the value of field folder_status
     *
     * @param integer $folder_status
     * @return $this
     */
    public function setFolderStatus($folder_status)
    {
        $this->folder_status = $folder_status;

        return $this;
    }
    /**
     * Method to set the value of field folder_link
     *
     * @param string $folder_link
     * @return $this
     */
    public function setFolderLink($folder_link)
    {
        $this->folder_link = $folder_link;

        return $this;
    }
    /**
     * Method to set the value of field folder_link_comment
     *
     * @param string $folder_link_comment
     * @return $this
     */
    public function setFolderLinkComment($folder_link_comment)
    {
        $this->folder_link_comment = $folder_link_comment;

        return $this;
    }
    /**
     * Method to set the value of field folder_color
     *
     * @param string $folder_color
     * @return $this
     */
    public function setFolderColor($folder_color)
    {
        $this->folder_color = $folder_color;

        return $this;
    }
    /**
     * Returns the value of field folder_id
     *
     * @return integer
     */
    public function getFolderId()
    {
        return $this->folder_id;
    }

    /**
     * Returns the value of field folder_test_id
     *
     * @return integer
     */
    public function getFolderTestId()
    {
        return $this->folder_test_id;
    }
    /**
     * Returns the value of field folder_status
     *
     * @return integer
     */
    public function getFolderStatus()
    {
        return $this->folder_status;
    }

    /**
     * Returns the value of field folder_link
     *
     * @return string
     */
    public function getFolderLink()
    {
        return $this->folder_link;
    }
    /**
     * Returns the value of field folder_link_comment
     *
     * @return string
     */
    public function getFolderLinkComment()
    {
        return $this->folder_link_comment;
    }
    /**
     * Returns the value of field folder_color
     *
     * @return string
     */
    public function getFolderColor()
    {
        return $this->folder_color;
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
        return 'learn_test_folder';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnTestFolder[]|LearnTestFolder
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnTestFolder
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstByTestIdAndFolderStatus($testId, $folderStatus){
        return LearnTestFolder::findFirst([
            'folder_test_id = :test_id: AND folder_status = :folder_status:',
            'bind' => ['test_id' => $testId, 'folder_status' => $folderStatus]
        ]);
    }

}
