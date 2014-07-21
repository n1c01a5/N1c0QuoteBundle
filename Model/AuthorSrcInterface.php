<?php

namespace N1c0\QuoteBundle\Model;

Interface AuthorSrcInterface
{
    /**
     * @return mixed unique ID for this authorSrc
     */
    public function getId();
    
    /**
     * Set name
     *
     * @param string $name
     * @return AuthorSrcInterface
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return AuthorSrcInterface
     */
    public function setFirstName($firstname);

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstName();
    /**
     * @return QuoteInterface
     */
    public function getQuote();

    /**
     * @param QuoteInterface $quote
     */
    public function setQuote(QuoteInterface $quote);
}
