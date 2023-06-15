<?php

namespace Learncom\Models;

class LearnArticle extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $article_id;

    /**
     *
     * @var string
     * @Column(type="string", length=256, nullable=false)
     */
    protected $article_name;

    /**
     *
     * @var string
     * @Column(type="string", length=256, nullable=true)
     */
    protected $article_icon;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_active;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_content;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_keyword;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $article_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $article_summary;

    /**
     * Method to set the value of field article_id
     *
     * @param integer $article_id
     * @return $this
     */
    public function setArticleId($article_id)
    {
        $this->article_id = $article_id;

        return $this;
    }

    /**
     * Method to set the value of field article_name
     *
     * @param string $article_name
     * @return $this
     */
    public function setArticleName($article_name)
    {
        $this->article_name = $article_name;

        return $this;
    }

    /**
     * Method to set the value of field article_icon
     *
     * @param string $article_icon
     * @return $this
     */
    public function setArticleIcon($article_icon)
    {
        $this->article_icon = $article_icon;

        return $this;
    }

    /**
     * Method to set the value of field article_active
     *
     * @param string $article_active
     * @return $this
     */
    public function setArticleActive($article_active)
    {
        $this->article_active = $article_active;

        return $this;
    }

    /**
     * Method to set the value of field article_content
     *
     * @param string $article_content
     * @return $this
     */
    public function setArticleContent($article_content)
    {
        $this->article_content = $article_content;

        return $this;
    }

    /**
     * Method to set the value of field article_keyword
     *
     * @param string $article_keyword
     * @return $this
     */
    public function setArticleKeyword($article_keyword)
    {
        $this->article_keyword = $article_keyword;

        return $this;
    }

    /**
     * Method to set the value of field article_order
     *
     * @param integer $article_order
     * @return $this
     */
    public function setArticleOrder($article_order)
    {
        $this->article_order = $article_order;

        return $this;
    }

    /**
     * Method to set the value of field article_summary
     *
     * @param string $article_summary
     * @return $this
     */
    public function setArticleSummary($article_summary)
    {
        $this->article_summary = $article_summary;

        return $this;
    }

    /**
     * Returns the value of field article_id
     *
     * @return integer
     */
    public function getArticleId()
    {
        return $this->article_id;
    }

    /**
     * Returns the value of field article_name
     *
     * @return string
     */
    public function getArticleName()
    {
        return $this->article_name;
    }

    /**
     * Returns the value of field article_icon
     *
     * @return string
     */
    public function getArticleIcon()
    {
        return $this->article_icon;
    }

    /**
     * Returns the value of field article_active
     *
     * @return string
     */
    public function getArticleActive()
    {
        return $this->article_active;
    }

    /**
     * Returns the value of field article_content
     *
     * @return string
     */
    public function getArticleContent()
    {
        return $this->article_content;
    }

    /**
     * Returns the value of field article_keyword
     *
     * @return string
     */
    public function getArticleKeyword()
    {
        return $this->article_keyword;
    }

    /**
     * Returns the value of field article_order
     *
     * @return integer
     */
    public function getArticleOrder()
    {
        return $this->article_order;
    }

    /**
     * Returns the value of field article_summary
     *
     * @return string
     */
    public function getArticleSummary()
    {
        return $this->article_summary;
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
        return 'learn_article';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnArticle[]|LearnArticle
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return LearnArticle
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
