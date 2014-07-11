<?php

namespace N1c0\DissertationBundle\Acl;

use N1c0\DissertationBundle\Model\AuthorSrcInterface;
use N1c0\DissertationBundle\Model\SignedAuthorSrcInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements ACL checking using the Symfony2 Security component
 */
class SecurityAuthorSrcAcl implements AuthorSrcAclInterface
{
    /**
     * Used to retrieve ObjectIdentity instances for objects.
     *
     * @var ObjectIdentityRetrievalStrategyInterface
     */
    protected $objectRetrieval;

    /**
     * The AclProvider.
     *
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * The FQCN of the AuthorSrc object.
     *
     * @var string
     */
    protected $authorSrcClass;

    /**
     * The Class OID for the AuthorSrc object.
     *
     * @var ObjectIdentity
     */
    protected $oid;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface                 $securityContext
     * @param ObjectIdentityRetrievalStrategyInterface $objectRetrieval
     * @param MutableAclProviderInterface              $aclProvider
     * @param string                                   $authorSrcClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $authorSrcClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->authorSrcClass = $authorSrcClass;
        $this->oid               = new ObjectIdentity('class', $this->authorSrcClass);
    }

    /**
     * Checks if the Security token is allowed to create a new AuthorSrc.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified AuthorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canView(AuthorSrcInterface $authorSrc)
    {
        return $this->securityContext->isGranted('VIEW', $authorSrc);
    }

    /**
     * Checks if the Security token is allowed to edit the specified AuthorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canEdit(AuthorSrcInterface $authorSrc)
    {
        return $this->securityContext->isGranted('EDIT', $authorSrc);
    }

    /**
     * Checks if the Security token is allowed to delete the specified AuthorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return boolean
     */
    public function canDelete(AuthorSrcInterface $authorSrc)
    {
        return $this->securityContext->isGranted('DELETE', $authorSrc);
    }

    /**
     * Sets the default object Acl entry for the supplied AuthorSrc.
     *
     * @param  AuthorSrcInterface $authorSrc
     * @return void
     */
    public function setDefaultAcl(AuthorSrcInterface $authorSrc)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($authorSrc);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($authorSrc instanceof SignedAuthorSrcInterface &&
            null !== $authorSrc->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($authorSrc->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the AuthorSrc class.
     *
     * This needs to be re-run whenever the AuthorSrc class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->authorSrcClass);

        try {
            $acl = $this->aclProvider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            return;
        }

        $this->doInstallFallbackAcl($acl, new MaskBuilder());
        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs the default Class Ace entries into the provided $acl object.
     *
     * Override this method in a subclass to change what permissions are defined.
     * Once this method has been overridden you need to run the
     * `fos:authorSrc:installAces --flush` command
     *
     * @param  AclInterface $acl
     * @param  MaskBuilder  $builder
     * @return void
     */
    protected function doInstallFallbackAcl(AclInterface $acl, MaskBuilder $builder)
    {
        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPER_ADMIN'), $builder->get());

        $builder->reset();
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());
    }

    /**
     * Removes fallback Acl entries for the AuthorSrc class.
     *
     * This should be run when uninstalling the AuthorSrcBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->authorSrcClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

