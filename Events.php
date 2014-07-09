<?php

namespace N1c0\QuoteBundle;

final class Events
{
    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Quote.
     *
     * This event allows you to modify the data in the Quote prior
     * to persisting occuring. The listener receives a
     * N1c0\QuoteBundle\Event\QuotePersistEvent instance.
     *
     * Persisting of the quote can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const QUOTE_PRE_PERSIST = 'n1c0_quote.quote.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Quote.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Quote to be persisted before performing
     * those actions. The listener receives a
     * N1c0\QuoteBundle\Event\QuoteEvent instance.
     *
     * @var string
     */
    const QUOTE_POST_PERSIST = 'n1c0_quote.quote.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Quote.
     *
     * The listener receives a N1c0\QuoteBundle\Event\QuoteEvent
     * instance.
     *
     * @var string
     */
    const QUOTE_CREATE = 'n1c0_quote.quote.create';
}
