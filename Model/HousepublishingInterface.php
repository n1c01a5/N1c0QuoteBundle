<?php

namespace N1c0\QuoteBundle\Model;

Interface HousepublishingInterface
{
    const STATE_VISIBLE = 0;
    const STATE_DELETED = 1;
    const STATE_SPAM = 2;
    const STATE_PENDING = 3;

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
