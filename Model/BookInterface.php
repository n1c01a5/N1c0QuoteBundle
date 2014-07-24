<?php

namespace N1c0\QuoteBundle\Model;

Interface BookInterface
{
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
}
