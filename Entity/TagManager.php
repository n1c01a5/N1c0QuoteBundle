<?php

namespace N1c0\QuoteBundle\Entity;

use Doctrine\ORM\EntityManager;
use N1c0\QuoteBundle\Model\TagManager as BaseTagManager;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\TagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM TagManager.
 *
 */
class TagManager extends BaseTagManager
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
     * Returns a flat array of tags of a specific quote.
     *
     * @param  QuoteInterface $quote
     * @return array           of QuoteInterface
     */
    public function findTagsByQuote(QuoteInterface $quote)
    {
        $qb = $this->repository
                ->createQueryBuilder('t')
                ->join('t.quotes', 'd')
                ->where('d.id = :quotes')
                ->setParameter('quotes', $quote->getId());

        $tags = $qb
            ->getQuery()
            ->execute();

        return $tags;
    }

    /**
     * Find one tag by its ID
     *
     * @param  array           $criteria
     * @return TagInterface
     */
    public function findQuoteById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Finds all Tags.
     *
     * @return array of TagInterface
     */
    public function findAllTags()
    {
        return $this->repository->findAll();
    }

    /**
     *
     * {@inheritDoc}
     *
    */
    public function isNewTag(TagInterface $tag)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($tag);
    }

    /**
     * Performs persisting of the tag.
     *
     * @param TagInterface $tag
     */
    protected function doSaveTag(TagInterface $tag)
    {
        $persistTag = true;

        $tags = $this->findAllTags();
        foreach($tags as $tagbdd) {
            if($tagbdd->getTitle() == $tag->getTitle()) {
                $quote->addTag($tagbdd);
                $this->em->persist($tagbdd);
                $this->em->persist($tag->getQuote());
                $persistTag = false;
            }
        }

       if($persistTag) {
            $this->em->persist($tag);
       }

        $this->em->flush();
    }

    /**
     * Returns the fully qualified tag quote class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
