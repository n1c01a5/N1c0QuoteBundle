<?php

namespace N1c0\QuoteBundle\Model;

use DateTime;

/**
 * Storage agnostic housepublishing quote object
 */
abstract class Housepublishing implements HousepublishingInterface
{
    /**
     * Housepublishing id 
     *
     * @var mixed
     */
    protected $id;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

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
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param  string
     * @return null
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return array with the names of the housepublishing authors
     */
    public function getAuthorsName()
    {
        return 'Anonymous';
    }

    /**
     * @return array with the name of the housepublishing author
     */
    public function getAuthorName()
    {
        return 'Anonymous';
    }

    public function __toString()
    {
        return 'Housepublishing #'.$this->getId();
    }
}
