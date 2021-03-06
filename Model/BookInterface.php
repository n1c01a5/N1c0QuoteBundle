<?php

namespace N1c0\QuoteBundle\Model;

Interface BookInterface
{
    const STATE_VISIBLE = 0;
    const STATE_DELETED = 1;
    const STATE_SPAM = 2;
    const STATE_PENDING = 3;

    /**
     * @return mixed unique ID for this book
     */
    public function getId();

    /**
     * @return array with authors of the quote
     */
    public function getAuthorsName();

    /**
     * Set title
     *
     * @param string $title
     * @return BookInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set body
     *
     * @param string $body
     * @return BookInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string
     */
    public function getBody();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return QuoteInterface
     */
    public function getQuote();

    /**
     * @param QuoteInterface $quote
     */
    public function setQuote(QuoteInterface $quote);

    /**
     * @return integer The current state of the comment
     */
    public function getState();

    /**
     * @param integer state
     */
    public function setState($state);

    /**
     * Gets the previous state.
     *
     * @return integer
     */
    public function getPreviousState();
}
