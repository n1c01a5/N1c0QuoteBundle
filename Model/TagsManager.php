<?php

namespace N1c0\DissertationBundle\Model;

use N1c0\DissertationBundle\Events;
use N1c0\DissertationBundle\Event\TagsEvent;
use N1c0\DissertationBundle\Event\TagsPersistEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidTagsException;

/**
 * Abstract Tags Manager implementation which can be used as base class for your
 * concrete manager.
 */
abstract class TagsManager implements TagsManagerInterface
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
     * Get a list of Tagss.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), array('createdAt' => 'DESC'), $limit, $offset);
    }

    /**
     * @param  string          $id
     * @return TagsInterface
     */
    public function findTagsById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Returns an empty tags instance
     *
     * @return Tags
     */
    public function createTags(DissertationInterface $dissertation)
    {
        $class = $this->getClass();
        $tags = new $class;

        $tags->setDissertation($dissertation);

        $event = new TagsEvent($tags);
        $this->dispatcher->dispatch(Events::TAGS_CREATE, $event);

        return $tags;
    }

    /**
     * Saves a tags to the persistence backend used. Each backend
     * must implement the abstract doSaveTags method which will
     * perform the saving of the tags to the backend.
     *
     * @param  TagsInterface         $tags
     * @throws InvalidTagsException when the tags does not have a dissertation.
     */
    public function saveTags(TagsInterface $tags)
    {
        if (null === $tags->getDissertation()) {
            throw new InvalidTagsException('The tags must have a dissertation');
        }

        $event = new TagsPersistEvent($tags);
        $this->dispatcher->dispatch(Events::TAGS_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveTags($tags);

        $event = new TagsEvent($tags);
        $this->dispatcher->dispatch(Events::TAGS_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a tags.
     *
     * @abstract
     * @param TagsInterface $tags
     */
    abstract protected function doSaveTags(TagsInterface $tags);
}
