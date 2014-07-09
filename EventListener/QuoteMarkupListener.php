<?php

namespace N1c0\QuoteBundle\EventListener;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\QuoteEvent;
use N1c0\QuoteBundle\Markup\ParserInterface;
use N1c0\QuoteBundle\Model\RawQuoteInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Parses a quote for markup and sets the result
 * into the rawBody property.
 *
 * @author Wagner Nicolas <contact@wagner-nicolas.com>
 */
class QuoteMarkupListener implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param \N1c0\QuoteBundle\Markup\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses raw quote data and assigns it to the rawBody
     * property.
     *
     * @param \N1c0\QuoteBundle\Event\QuoteEvent $event
     */
    public function markup(QuoteEvent $event)
    {
        $quote = $event->getQuote();

        if (!$quote instanceof RawQuoteInterface) {
            return;
        }

        $result = $this->parser->parse($quote->getBody());
        $quote->setRawBody($result);
    }

    public static function getSubscribedEvents()
    {
        return array(Events::QUOTE_PRE_PERSIST => 'markup');
    }
}
