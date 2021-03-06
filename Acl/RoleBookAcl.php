<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\BookInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements Role checking using the Symfony2 Security component
 */
class RoleBookAcl implements BookAclInterface
{
    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Book object.
     *
     * @var string
     */
    private $bookClass;

    /**
     * The role that will grant create permission for a book.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a book.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a book.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a book.
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
     * @param string                   $bookClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $bookClass
    )
    {
        $this->securityContext   = $securityContext;
        $this->createRole        = $createRole;
        $this->viewRole          = $viewRole;
        $this->editRole          = $editRole;
        $this->deleteRole        = $deleteRole;
        $this->bookClass      = $bookClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Book.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Book.
     *
     * @param  BookInterface $book
     * @return boolean
     */
    public function canView(BookInterface $book)
    {
        return $this->securityContext->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token is allowed to reply to a parent book.
     *
     * @param  BookInterface|null $parent
     * @return boolean
     */
    public function canReply(BookInterface $parent = null)
    {
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent);
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Book.
     *
     * @param  BookInterface $book
     * @return boolean
     */
    public function canEdit(BookInterface $book)
    {
        return $this->securityContext->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Book.
     *
     * @param  BookInterface $book
     * @return boolean
     */
    public function canDelete(BookInterface $book)
    {
        return $this->securityContext->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param  BookInterface $book
     * @return void
     */
    public function setDefaultAcl(BookInterface $book)
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
