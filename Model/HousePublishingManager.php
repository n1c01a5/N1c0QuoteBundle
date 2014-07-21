<?php

namespace N1c0\QuoteBundle\Model;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\HousePublishingEvent;
use N1c0\QuoteBundle\Event\HousePublishingPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidHousePublishingException;

/**
 * Abstract HousePublishing Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class HousePublishingManager implements HousePublishingManagerInterface
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
     * Get a list of HousePublishings.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), array(), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return HousePublishingInterface
     */
    public function findHousePublishingById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty housePublishing instance
     *
     * @return HousePublishing
     */
    public function createHousePublishing(QuoteInterface $quote)
    {
        $class = $this->getClass();
        $housePublishing = new $class;

        $housePublishing->setQuote($quote);

        $event = new HousePublishingEvent($housePublishing);
        $this->dispatcher->dispatch(Events::HOUSEPUBLISHING_CREATE, $event);

        return $housePublishing;
    }

    /**
     * Saves a housePublishing to the persistence backend used. Each backend
     * must implement the abstract doSaveHousePublishing method which will
     * perform the saving of the housePublishing to the backend.
     *
     * @param  HousePublishingInterface         $housePublishing
     * @throws InvalidHousePublishingException when the housePublishing does not have a quote.
     */
    public function saveHousePublishing(HousePublishingInterface $housePublishing)
    {
        if (null === $housePublishing->getQuote()) {
            throw new InvalidHousePublishingException('The housePublishing must have a quote');
        }

        $event = new HousePublishingPersistEvent($housePublishing);
        $this->dispatcher->dispatch(Events::HOUSEPUBLISHING_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveHousePublishing($housePublishing);

        $event = new HousePublishingEvent($housePublishing);
        $this->dispatcher->dispatch(Events::HOUSEPUBLISHING_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a housePublishing.
     *
     * @abstract
     * @param HousePublishingInterface $housePublishing
     */
    abstract protected function doSaveHousePublishing(HousePublishingInterface $housePublishing);
}
