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
     * persisting the AuthorSrc.
     *
     * This event allows you to modify the data in the AuthorSrc prior
     * to persisting occuring. The listener receives a
     * N1c0\QuoteBundle\Event\AuthorSrcPersistEvent instance.
     *
     * Persisting of the quote can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const AUTHORSRC_PRE_PERSIST = 'n1c0_quote.authorsrc.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the AuthorSrc.
     *
     * This event allows you to notify users or perform other actions
     * that might require the AuthorSrc to be persisted before performing
     * those actions. The listener receives a
     * N1c0\QuoteBundle\Event\AuthorSrcEvent instance.
     *
     * @var string
     */
    const AUTHORSRC_POST_PERSIST = 'n1c0_quote.authorsrc.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a AuthorSrc.
     *
     * The listener receives a N1c0\QuoteBundle\Event\AuthorSrcEvent
     * instance.
     *
     * @var string
     */
    const AUTHORSRC_CREATE = 'n1c0_quote.authorsrc.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the HousePublishing.
     *
     * This event allows you to modify the data in the AuthorSrc prior
     * to persisting occuring. The listener receives a
     * N1c0\QuoteBundle\Event\HousePublishingPersistEvent instance.
     *
     * Persisting of the quote can be aborted by calling
     * $event->abortPersist()
     *
     * @var string
     */
    const HOUSEPUBLISHING_PRE_PERSIST = 'n1c0_quote.housepublishing.pre_persist';

    /**
     * The POST_PERSIST event occurs after the persistence backend
     * persisted the HousePublishing.
     *
     * This event allows you to notify users or perform other actions
     * that might require the HousePublishing to be persisted before performing
     * those actions. The listener receives a
     * N1c0\QuoteBundle\Event\HousePublishingEvent instance.
     *
     * @var string
     */
    const HOUSEPUBLISHING_POST_PERSIST = 'n1c0_quote.housepublishing.post_persist';

    /**
     * The CREATE event occurs when the manager is asked to create
     * a new instance of a HousePublishing.
     *
     * The listener receives a N1c0\QuoteBundle\Event\HousePublishingEvent
     * instance.
     *
     * @var string
     */
    const HOUSEPUBLISHING_CREATE = 'n1c0_quote.housepublishing.create';

    /**
     * The PRE_PERSIST event occurs prior to the persistence backend
     * persisting the Tag.
     *
     * This event allows you to modify the data in the AuthorSrc prior
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
}
