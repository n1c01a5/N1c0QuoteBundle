<?php

namespace N1c0\QuoteBundle\Model;

Interface TagInterface
{
    const STATE_VISIBLE = 0;
    const STATE_DELETED = 1;
    const STATE_SPAM = 2;
    const STATE_PENDING = 3;

    /**
     * @return mixed unique ID for this tag
     */
    public function getId();

    /**
     * Set title
     *
     * @param string $title
     * @return TagInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * @return integer The current state of the comment
     */
    public function getState();

    /**
     * @param integer state
     */
    public function setState($state);

    /**
     * Gets the previous state.
     *
     * @return integer
     */
    public function getPreviousState();
}
