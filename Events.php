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

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Authorsrc.
     *
     * This event allows you to modify the data in the Authorsrc prior
     * to persisting occuring. The listener receives a
     * N1c0\QuoteBundle\Event\AuthorsrcPersistEvent instance.
     *
     * Persisting of the quote can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const AUTHORSRC_PRE_PERSIST = 'n1c0_quote.authorsrc.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Authorsrc.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Authorsrc to be persisted before performing
     * those actions. The listener receives a
     * N1c0\QuoteBundle\Event\AuthorsrcEvent instance.
     *
     * @var string
     */
    const AUTHORSRC_POST_PERSIST = 'n1c0_quote.authorsrc.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Authorsrc.
     *
     * The listener receives a N1c0\QuoteBundle\Event\AuthorsrcEvent
     * instance.
     *
     * @var string
     */
    const AUTHORSRC_CREATE = 'n1c0_quote.authorsrc.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Housepublishing.
     *
     * This event allows you to modify the data in the Authorsrc prior
     * to persisting occuring. The listener receives a
     * N1c0\QuoteBundle\Event\HousepublishingPersistEvent instance.
     *
     * Persisting of the quote can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const HOUSEPUBLISHING_PRE_PERSIST = 'n1c0_quote.housepublishing.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Housepublishing.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Housepublishing to be persisted before performing
     * those actions. The listener receives a
     * N1c0\QuoteBundle\Event\HousepublishingEvent instance.
     *
     * @var string
     */
    const HOUSEPUBLISHING_POST_PERSIST = 'n1c0_quote.housepublishing.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Housepublishing.
     *
     * The listener receives a N1c0\QuoteBundle\Event\HousepublishingEvent
     * instance.
     *
     * @var string
     */
    const HOUSEPUBLISHING_CREATE = 'n1c0_quote.housepublishing.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Tag.
     *
     * This event allows you to modify the data in the Authorsrc prior
     * to persisting occuring. The listener receives a
     * N1c0\QuoteBundle\Event\TagPersistEvent instance.
     *
     * Persisting of the quote can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const TAG_PRE_PERSIST = 'n1c0_quote.tag.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Tag.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Tag to be persisted before performing
     * those actions. The listener receives a
     * N1c0\QuoteBundle\Event\TagEvent instance.
     *
     * @var string
     */
    const TAG_POST_PERSIST = 'n1c0_quote.tag.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Tag.
     *
     * The listener receives a N1c0\QuoteBundle\Event\TagEvent
     * instance.
     *
     * @var string
     */
    const TAG_CREATE = 'n1c0_quote.tag.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Book.
     *
     * This event allows you to modify the data in the Book prior
     * to persisting occuring. The listener receives a
     * N1c0\QuoteBundle\Event\BookPersistEvent instance.
     *
     * Persisting of the quote can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const BOOK_PRE_PERSIST = 'n1c0_quote.book.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the Quote.
     *
     * This event allows you to notify users or perform other actions
     * that might require the Quote to be persisted before performing
     * those actions. The listener receives a
     * N1c0\QuoteBundle\Event\BookEvent instance.
     *
     * @var string
     */
    const BOOK_POST_PERSIST = 'n1c0_quote.book.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a Book.
     *
     * The listener receives a N1c0\QuoteBundle\Event\BookEvent
     * instance.
     *
     * @var string
     */
    const BOOK_CREATE = 'n1c0_quote.book.create';
}
