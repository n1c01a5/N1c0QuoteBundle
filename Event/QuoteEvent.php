<?php

namespace N1c0\QuoteBundle\Event;

use N1c0\QuoteBundle\Model\QuoteInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a quote.
 */
class QuoteEvent extends Event
{
    private $quote;

    /**
     * Constructs an event.
     *
     * @param \N1c0\QuoteBundle\Model\QuoteInterface $quote
     */
    public function __construct(QuoteInterface $quote)
    {
        $this->quote = $quote;
    }

    /**
     * Returns the quote for this event.
     *
     * @return \N1c0\QuoteBundle\Model\QuoteInterface
     */
    public function getQuote()
    {
        return $this->quote;
    }
}
