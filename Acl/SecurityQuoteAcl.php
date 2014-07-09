<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Model\SignedQuoteInterface;
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
class SecurityQuoteAcl implements QuoteAclInterface
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
     * The FQCN of the Quote object.
     *
     * @var string
     */
    protected $quoteClass;

    /**
     * The Class OID for the Quote object.
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
     * @param string                                   $quoteClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $quoteClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->quoteClass      = $quoteClass;
        $this->oid               = new ObjectIdentity('class', $this->quoteClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Quote.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canView(QuoteInterface $quote)
    {
        return $this->securityContext->isGranted('VIEW', $quote);
    }


    /**
     * Checks if the Security token is allowed to edit the specified Quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canEdit(QuoteInterface $quote)
    {
        return $this->securityContext->isGranted('EDIT', $quote);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canDelete(QuoteInterface $quote)
    {
        return $this->securityContext->isGranted('DELETE', $quote);
    }

    /**
     * Sets the default object Acl entry for the supplied Quote.
     *
     * @param  QuoteInterface $quote
     * @return void
     */
    public function setDefaultAcl(QuoteInterface $quote)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($quote);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($quote instanceof SignedQuoteInterface &&
            null !== $quote->getAuthor()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($quote->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Quote class.
     *
     * This needs to be re-run whenever the Quote class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->quoteClass);

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
     * `fos:quote:installAces --flush` command
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
     * Removes fallback Acl entries for the Quote class.
     *
     * This should be run when uninstalling the QuoteBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->quoteClass);
        $this->aclProvider->deleteAcl($oid);
    }
}

