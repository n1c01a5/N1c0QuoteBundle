<?php

namespace N1c0\QuoteBundle\Model;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\QuoteEvent;
use N1c0\QuoteBundle\Event\QuotePersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract Quote Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class QuoteManager implements QuoteManagerInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get a list of Quotes.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit, $offset)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Get a list of Quotes.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function by(array $criteria, $limit, $offset)
    {
        return $this->repository->findBy($criteria, null, $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return QuoteInterface
     */
    public function findQuoteById($id)
    {
        return $this->findQuoteBy(array('id' => $id));
    }

    /**
     * Creates an empty element quote instance
     *
     * @return Quote
     */
    public function createQuote($id = null)
    {
        $class = $this->getClass();
        $quote = new $class;

        if (null !== $id) {
            $quote->setId($id);
        }

        $event = new QuoteEvent($quote);
        $this->dispatcher->dispatch(Events::QUOTE_CREATE, $event);

        return $quote;
    }

    /**
     * Persists a quote.
     *
     * @param QuoteInterface $quote
     */
    public function saveQuote(QuoteInterface $quote)
    {
        $event = new QuotePersistEvent($quote);
        $this->dispatcher->dispatch(Events::QUOTE_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveQuote($quote);

        $event = new QuoteEvent($quote);
        $this->dispatcher->dispatch(Events::QUOTE_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of the Quote.
     *
     * @abstract
     * @param QuoteInterface $quote
     */
    abstract protected function doSaveQuote(QuoteInterface $quote);
}
