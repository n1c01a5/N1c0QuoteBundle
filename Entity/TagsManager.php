<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\TagsManager as BaseTagsManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\TagsInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM TagsManager.
 *
 */
class TagsManager extends BaseTagsManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher 
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param string                                                      $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        parent::__construct($dispatcher);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Returns a flat array of tagss of a specific dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of DissertationInterface
     */
    public function findTagssByDissertation(DissertationInterface $dissertation)
    {
        $qb = $this->repository
                ->createQueryBuilder('i')
                ->join('i.dissertation', 'd')
                ->where('d.id = :dissertation')
                ->add('orderBy', 'p.createdAt DESC')
                ->setParameter('dissertation', $dissertation->getId());

        $tagss = $qb
            ->getQuery()
            ->execute();

        return $tagss;
    }

    /**
     * Find one tags by its ID
     *
     * @param  array           $criteria
     * @return TagsInterface
     */
    public function findDissertationById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Tagss.
     *
     * @return array of TagsInterface
     */
    public function findAllTagss()
    {
        return $this->repository->findAll();
    }

    /**
     * Performs persisting of the tags. 
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSaveTags(TagsInterface $tags)
    {
        $this->em->persist($tags->getDissertation());
        $this->em->persist($tags);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified tags dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
