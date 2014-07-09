<?php

namespace N1c0\QuoteBundle\Acl;

use N1c0\QuoteBundle\Model\QuoteInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 */
interface QuoteAclInterface
{
    /**
     * Checks if the user should be able to create a quote.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be able to view a quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canView(QuoteInterface $quote);

    /**
     * Checks if the user can reply to the supplied 'parent' quote
     * or if not supplied, just the ability to reply.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canReply(QuoteInterface $parent = null);

    /**
     * Checks if the user should be able to edit a quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canEdit(QuoteInterface $quote);

    /**
     * Checks if the user should be able to delete a quote.
     *
     * @param  QuoteInterface $quote
     * @return boolean
     */
    public function canDelete(QuoteInterface $quote);

    /**
     * Sets the default Acl permissions on a quote.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new QuoteInterface instances.
     *
     * @param  QuoteInterface $quote
     * @return void
     */
    public function setDefaultAcl(QuoteInterface $quote);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    public function installFallbackAcl();

    /**
     * Removes default Acl entries
     * @return void
     */
    public function uninstallFallbackAcl();
}
