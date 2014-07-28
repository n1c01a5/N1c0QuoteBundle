<?php

namespace N1c0\QuoteBundle\Model;

use N1c0\QuoteBundle\Events;
use N1c0\QuoteBundle\Event\TagEvent;
use N1c0\QuoteBundle\Event\TagPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidArgumentException;

/**
 * Abstract Tag Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class TagManager implements TagManagerInterface
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
     * Get a list of Tags.
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
     * @return TagInterface
     */
    public function findTagById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty tag instance
     *
     * @return Tag
     */
    public function createTag(QuoteInterface $quote)
    {
        $class = $this->getClass();
        $tag = new $class;

        $tag->addQuote($quote);

        $event = new TagEvent($tag);
        $this->dispatcher->dispatch(Events::TAG_CREATE, $event);

        return $tag;
    }

    /**
     * Saves a tag to the persistence backend used. Each backend
     * must implement the abstract doSaveTag method which will
     * perform the saving of the tag to the backend.
     *
     * @param  QuoteInterface         $quote
     * @param  TagInterface         $tag
     * @throws InvalidTagException when the tag does not have a quote.
     */
    public function saveTag(QuoteInterface $quote, TagInterface $tag)
    {
        if (null === $tag->getQuotes()) {
            throw new InvalidArgumentException('The tag must have a quote');
        }

        $event = new TagPersistEvent($tag);
        $this->dispatcher->dispatch(Events::TAG_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveTag($quote, $tag);

        $event = new TagEvent($tag);
        $this->dispatcher->dispatch(Events::TAG_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a tag.
     *
     * @abstract
     * @param QuoteInterface $quote
     * @param TagInterface $tag
     */
    abstract protected function doSaveTag(QuoteInterface $quote, TagInterface $tag);
}
