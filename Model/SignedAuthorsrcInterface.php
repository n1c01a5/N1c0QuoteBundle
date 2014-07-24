<?php

namespace N1c0\QuoteBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A signed authorsrc is bound to a FOS\UserBundle User model.
 */
interface SignedAuthorsrcInterface extends AuthorsrcInterface
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
     * Gets the authors of the Authorsrc
     *
     * @return UserInterface
     */
    public function getAuthors();

    /**
     * Gets the last author of the Authorsrc
     *
     * @return UserInterface
     */
    public function getAuthor();
}

