<?php

namespace N1c0\QuoteBundle\Model;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\HousepublishingEvent;
use N1c0\QuoteBundle\Event\HousepublishingPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidHousepublishingException;

/**
 * Abstract Housepublishing Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class HousepublishingManager implements HousepublishingManagerInterface
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
     * Get a list of Housepublishings.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit, $offset)
    {
        return $this->repository->findBy(array(), array(), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return HousepublishingInterface
     */
    public function findHousepublishingById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty housepublishing instance
     *
     * @return Housepublishing
     */
    public function createHousepublishing(QuoteInterface $quote)
    {
        $class = $this->getClass();
        $housepublishing = new $class;

        $housepublishing->setQuote($quote);

        $event = new HousepublishingEvent($housepublishing);
        $this->dispatcher->dispatch(Events::HOUSEPUBLISHING_CREATE, $event);

        return $housepublishing;
    }

    /**
     * Saves a housepublishing to the persistence backend used. Each backend
     * must implement the abstract doSaveHousepublishing method which will
     * perform the saving of the housepublishing to the backend.
     *
     * @param  HousepublishingInterface         $housepublishing
     * @throws InvalidHousepublishingException when the housepublishing does not have a quote.
     */
    public function saveHousepublishing(HousepublishingInterface $housepublishing)
    {
        if (null === $housepublishing->getQuote()) {
            throw new InvalidHousepublishingException('The housepublishing must have a quote');
        }

        $event = new HousepublishingPersistEvent($housepublishing);
        $this->dispatcher->dispatch(Events::HOUSEPUBLISHING_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveHousepublishing($housepublishing);

        $event = new HousepublishingEvent($housepublishing);
        $this->dispatcher->dispatch(Events::HOUSEPUBLISHING_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a housepublishing.
     *
     * @abstract
     * @param HousepublishingInterface $housepublishing
     */
    abstract protected function doSaveHousepublishing(HousepublishingInterface $housepublishing);
}
