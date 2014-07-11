<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\TagsBundle\Model\TagsInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleTagsAcl implements TagsAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Tags object.
     *
     * @var string
     */
    private $tagsClass;

    /**
     * The role that will grant create permission for a tags.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a tags.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a tags.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a tags.
     *
     * @var string
     */
    private $deleteRole;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param string                   $createRole
     * @param string                   $viewRole
     * @param string                   $editRole
     * @param string                   $deleteRole
     * @param string                   $tagsClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $tagsClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->tagsClass      = $tagsClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Tags.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canView(TagsInterface $tags)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent tags.
     *
     * @param  TagsInterface|null $parent
     * @return boolean
     */
    public function canReply(TagsInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canEdit(TagsInterface $tags)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Tags.
     *
     * @param  TagsInterface $tags
     * @return boolean
     */
    public function canDelete(TagsInterface $tags)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  TagsInterface $tags
     * @return void
     */
    public function setDefaultAcl(TagsInterface $tags)
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function installFallbackAcl()
    {

    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {

    }
}
