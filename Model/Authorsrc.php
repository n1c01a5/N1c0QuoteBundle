<?php

namespace N1c0\QuoteBundle\Model;

/**
 * Storage agnostic authorsrc quote object
 */
abstract class Authorsrc implements AuthorsrcInterface
{
    /**
     * Authorsrc id
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
     * Current state of the quote.
     *
     * @var integer
     */
    protected $state = 0;

    /**
     * The previous state of the quote.
     *
     * @var integer
     */
    protected $previousState = 0;

    /**
     * Firstname
     *
     * @var string
     */
    protected $firstname;

    /**
     * Birthday
     *
     * @var datetime
     */
    protected $birthday;

   /**
     * Website
     *
     * @var string
     */
    protected $website;

   /**
     * Bio
     *
     * @var text
     */
    protected $bio;

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
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstname;
    }

    /**
     * @param  string
     * @return null
     */
    public function setFirstName($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param  string
     * @return null
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param  string
     * @return null
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return text
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param  text
     * @return null
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
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
     * @return array with the names of the authorsrc authors
     */
    public function getAuthorsName()
    {
        return 'Anonymous';
    }

    /**
     * @return array with the name of the authorsrc author
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
        return 'Authorsrc #'.$this->getId();
    }
}
