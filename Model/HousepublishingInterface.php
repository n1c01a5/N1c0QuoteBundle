<?php

namespace N1c0\QuoteBundle\Model;

Interface HousepublishingInterface
{
    /**
     * @return mixed unique ID for this housepublishing
     */
    public function getId();
    
    /**
     * Set name 
     *
     * @param string $name
     * @return HousepublishingInterface
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * @return QuoteInterface
     */
    public function getQuote();

    /**
     * @param QuoteInterface $quote
     */
    public function setQuote(QuoteInterface $quote);
}
