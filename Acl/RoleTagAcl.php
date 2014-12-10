<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\TagInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleTagAcl implements TagAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Tag object.
     *
     * @var string
     */
    private $tagClass;

    /**
     * The role that will grant create permission for a tag.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a tag.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a tag.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a tag.
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
     * @param string                   $tagClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $tagClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->tagClass      = $tagClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Tag.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canView(TagInterface $tag)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent tag.
     *
     * @param  TagInterface|null $parent
     * @return boolean
     */
    public function canReply(TagInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canEdit(TagInterface $tag)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Tag.
     *
     * @param  TagInterface $tag
     * @return boolean
     */
    public function canDelete(TagInterface $tag)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  TagInterface $tag
     * @return void
     */
    public function setDefaultAcl(TagInterface $tag)
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
