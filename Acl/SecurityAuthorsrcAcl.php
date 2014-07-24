<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\AuthorsrcInterface;
use N1c0\QuoteBundle\Model\SignedAuthorsrcInterface;
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
class SecurityAuthorsrcAcl implements AuthorsrcAclInterface
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
     * The FQCN of the Authorsrc object.
     *
     * @var string
     */
    protected $authorsrcClass;

    /**
     * The Class OID for the Authorsrc object.
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
     * @param string                                   $authorsrcClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $authorsrcClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->authorsrcClass = $authorsrcClass;
        $this->oid               = new ObjectIdentity('class', $this->authorsrcClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Authorsrc.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canView(AuthorsrcInterface $authorsrc)
    {
        return $this->securityContext->isGranted('VIEW', $authorsrc);
    }

    /**
     * Checks if the Security token is allowed to edit the specified Authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canEdit(AuthorsrcInterface $authorsrc)
    {
        return $this->securityContext->isGranted('EDIT', $authorsrc);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return boolean
     */
    public function canDelete(AuthorsrcInterface $authorsrc)
    {
        return $this->securityContext->isGranted('DELETE', $authorsrc);
    }

    /**
     * Sets the default object Acl entry for the supplied Authorsrc.
     *
     * @param  AuthorsrcInterface $authorsrc
     * @return void
     */
    public function setDefaultAcl(AuthorsrcInterface $authorsrc)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($authorsrc);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($authorsrc instanceof SignedAuthorsrcInterface &&
            null !== $authorsrc->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($authorsrc->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Authorsrc class.
     *
     * This needs to be re-run whenever the Authorsrc class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->authorsrcClass);

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
     * `fos:authorsrc:installAces --flush` command
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
     * Removes fallback Acl entries for the Authorsrc class.
     *
     * This should be run when uninstalling the AuthorsrcBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->authorsrcClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

