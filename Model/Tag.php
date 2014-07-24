<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Storage agnostic tag quote object
 */
abstract class Tag implements TagInterface
{
    /**
     * Tag id 
     *
     * @var mixed
     */
    protected $id;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Should be mapped by the end developer.
     *
     * @var QuoteInterface
     */
    protected $quote;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @param  string
     * @return null
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return QuoteInterface
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param QuoteInterface $quote
     *
     * @return void
     */
    public function setQuote(QuoteInterface $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return array with the names of the tag authors
     */
    public function getAuthorsName()
    {
        return 'Anonymous';
    }

    /**
     * @return array with the name of the tag author
     */
    public function getAuthorName()
    {
        return 'Anonymous';
    }

    public function __toString()
    {
        return 'Tag #'.$this->getId();
    }
}
