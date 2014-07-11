<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\TagsInterface;
use N1c0\DissertationBundle\Model\TagsManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of TagsManagerInterface and
 * performs Acl checks with the configured Tags Acl service.
 */
class AclTagsManager implements TagsManagerInterface
{
    /**
     * The TagsManager instance to be wrapped with ACL.
     *
     * @var TagsManagerInterface
     */
    protected $realManager;

    /**
     * The TagsAcl instance for checking permissions.
     *
     * @var TagsAclInterface
     */
    protected $tagsAcl;

    /**
     * Constructor.
     *
     * @param TagsManagerInterface $tagsManager The concrete TagsManager service
     * @param TagsAclInterface     $tagsAcl     The Tags Acl service
     */
    public function __construct(TagsManagerInterface $tagsManager, TagsAclInterface $tagsAcl)
    {
        $this->realManager      = $tagsManager;
        $this->tagsAcl  = $tagsAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function all($limit = 5, $offset = 0)
    {
        $tagss = $this->realManager->all();

        if (!$this->authorizeViewTags($tagss)) {
            throw new AccessDeniedException();
        }

        return $tagss;
    }

    /**
     * {@inheritDoc}
     */
    public function findTagsBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findTagssBy(array $criteria){
    }

    /**
     * {@inheritDoc}
     */
    public function findAllTagss(){
    }                 


    /**
     * {@inheritDoc}
     */
    public function saveTags(TagsInterface $tags)
    {
        if (!$this->tagsAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newTags = $this->isNewTags($tags);

        if (!$newTags && !$this->tagsAcl->canEdit($tags)) {
            throw new AccessDeniedException();
        }

        if (($tags::STATE_DELETED === $tags->getState() || $tags::STATE_DELETED === $tags->getPreviousState())
            && !$this->tagsAcl->canDelete($tags)
        ) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveTags($tags);

        if ($newTags) {
            $this->tagsAcl->setDefaultAcl($tags);
        }
    }

    /**
     * {@inheritDoc}
     **/
    public function findTagsById($id)
    {
        $tags = $this->realManager->findTagsById($id);

        if (null !== $tags && !$this->tagsAcl->canView($tags)) {
            throw new AccessDeniedException();
        }

        return $tags;
    }

    /**
     * {@inheritDoc}
     */
    public function createTags($id = null)
    {
        return $this->realManager->createTags($id);
    }

    /**
     * {@inheritDoc}
     */
    public function isNewTags(TagsInterface $tags)
    {
        return $this->realManager->isNewTags($tags);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Check if the tags have appropriate view permissions.
     *
     * @param  array   $tagss A comment tree
     * @return boolean
     */
    protected function authorizeViewTags(array $tagss)
    {
        foreach ($tagss as $tags) {
            if (!$this->tagsAcl->canView($tags)) {
                return false;
            }
        }

        return true;
    }
}
