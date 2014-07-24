<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\TagInterface;
use N1c0\QuoteBundle\Model\TagManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of TagManagerInterface and
 * performs Acl checks with the configured Tag Acl service.
 */
class AclTagManager implements TagManagerInterface
{
    /**
     * The TagManager instance to be wrapped with ACL.
     *
     * @var TagManagerInterface
     */
    protected $realManager;

    /**
     * The TagAcl instance for checking permissions.
     *
     * @var TagAclInterface
     */
    protected $tagAcl;

    /**
     * Constructor.
     *
     * @param TagManagerInterface $tagManager The concrete TagManager service
     * @param TagAclInterface     $tagAcl     The Tag Acl service
     */
    public function __construct(TagManagerInterface $tagManager, TagAclInterface $tagAcl)
    {
        $this->realManager      = $tagManager;
        $this->tagAcl  = $tagAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $tags = $this->realManager->all();

        if (!$this->authorizeViewTag($tags)) {
            throw new AccessDeniedException();
        }

        return $tags;
    }

    /**
     * {@inheritDoc}
     */
    public function findTagBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findTagsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllTags(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveTag(TagInterface $tag)
    {
        if (!$this->tagAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newTag = $this->isNewTag($tag);

        if (!$newTag && !$this->tagAcl->canEdit($tag)) {
            throw new AccessDeniedException();
        }

        if (($tag::STATE_DELETED === $tag->getState() || $tag::STATE_DELETED === $tag->getPreviousState())
            && !$this->tagAcl->canDelete($tag)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveTag($tag);

        if ($newTag) {
            $this->tagAcl->setDefaultAcl($tag);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findTagById($id)
    {
        $tag = $this->realManager->findTagById($id);

        if (null !== $tag && !$this->tagAcl->canView($tag)) {
            throw new AccessDeniedException();
        }

        return $tag;
    }

    /**
     * {@inheritDoc}
     */
    public function createTag($id = null)
    {
        return $this->realManager->createTag($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewTag(TagInterface $tag)
    {
        return $this->realManager->isNewTag($tag);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the tag have appropriate view permissions.
     *
     * @param  array   $tags A comment tree
     * @return boolean
     */
    protected function authorizeViewTag(array $tags)
    {
        foreach ($tags as $tag) {
            if (!$this->tagAcl->canView($tag)) {
                return false;
            }
        }

        return true;
    }
}
