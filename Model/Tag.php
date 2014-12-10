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
     * Current state of the tag.
     *
     * @var integer
     */
    protected $state = 0;

    /**
     * The previous state of the Tag.
     *
     * @var integer
     */
    protected $previousState = 0;

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
    protected $quotes;

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
    public function getQuotes()
    {
        return $this->quotes;
    }

    /**
     * @param QuoteInterface $quote
     *
     * @return void
     */
    public function addQuotes(QuoteInterface $quote)
    {
        $this->quotes[] = $quote;
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

    /**
     * {@inheritDoc}
     */
    public function getState()
    {
        return $this->state;
    }

   /**
     * {@inheritDoc}
     */
    public function setState($state)
    {
        $this->previousState = $this->state;
        $this->state = $state;
    }

    /**
     * {@inheritDoc}
     */
    public function getPreviousState()
    {
        return $this->previousState;
    }

    public function __toString()
    {
        return 'Tag #'.$this->getId();
    }
}
