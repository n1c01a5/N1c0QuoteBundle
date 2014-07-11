<?php

namespace N1c0\DissertationBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\DissertationBundle\Model\AuthorSrcManager as BaseAuthorSrcManager;
use N1c0\DissertationBundle\Model\DissertationInterface;
use N1c0\DissertationBundle\Model\AuthorSrcInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM AuthorSrcManager.
 *
 */
class AuthorSrcManager extends BaseAuthorSrcManager
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
     * Returns a flat array of authorSrcs of a specific dissertation.
     *
     * @param  DissertationInterface $dissertation
     * @return array           of DissertationInterface
     */
    public function findAuthorSrcsByDissertation(DissertationInterface $dissertation)
    {
        $qb = $this->repository
                ->createQueryBuilder('i')
                ->join('i.dissertation', 'd')
                ->where('d.id = :dissertation')
                ->add('orderBy', 'p.createdAt DESC')
                ->setParameter('dissertation', $dissertation->getId());

        $authorSrcs = $qb
            ->getQuery()
            ->execute();

        return $authorSrcs;
    }

    /**
     * Find one authorSrc by its ID
     *
     * @param  array           $criteria
     * @return AuthorSrcInterface
     */
    public function findDissertationById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all AuthorSrcs.
     *
     * @return array of AuthorSrcInterface
     */
    public function findAllAuthorSrcs()
    {
        return $this->repository->findAll();
    }

    /**
     * Performs persisting of the authorSrc. 
     *
     * @param DissertationInterface $dissertation
     */
    protected function doSaveAuthorSrc(AuthorSrcInterface $authorSrc)
    {
        $this->em->persist($authorSrc->getDissertation());
        $this->em->persist($authorSrc);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified authorSrc dissertation class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
