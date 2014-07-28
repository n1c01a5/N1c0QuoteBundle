<?php

namespace N1c0\QuoteBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * Housepublishing form creator
 */
interface HousepublishingFormFactoryInterface
{
    /**
     * Creates a chapter form
     *
     * @return FormInterface
     */
    public function createForm();
}
