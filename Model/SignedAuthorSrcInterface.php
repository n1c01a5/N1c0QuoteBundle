<?php

namespace N1c0\QuoteBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A signed authorSrc is bound to a FOS\UserBundle User model.
 */
interface SignedAuthorSrcInterface extends AuthorSrcInterface
{
    /**
     * Add user 
     *
     * @param Application\UserBundle\Entity\User $user
     */
    public function addAuthor(\Application\UserBundle\Entity\User $user);

    /**
     * Remove user
     *
     * @param Application\UserBundle\Entity\User $user
     */
    public function removeUser(\Application\UserBundle\Entity\User $user);

    /**
     * Gets the authors of the AuthorSrc
     *
     * @return UserInterface
     */
    public function getAuthors();

    /**
     * Gets the last author of the AuthorSrc
     *
     * @return UserInterface
     */
    public function getAuthor();
}

