<?php

namespace N1c0\QuoteBundle\Model;

Interface HousePublishingInterface
{
    /**
     * @return mixed unique ID for this housePublishing
     */
    public function getId();
    
    /**
     * Set name 
     *
     * @param string $name
     * @return HousePublishingInterface
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
