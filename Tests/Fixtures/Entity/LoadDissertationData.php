<?php

namespace N1c0\QuoteBundle\Tests\Fixtures\Entity;

use N1c0\QuoteBundle\Entity\Quote;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadQuoteData implements FixtureInterface
{
    static public $quotes = array();

    public function load(ObjectManager $manager)
    {
        $quote = new Quote();
        $quote->setTitle('title');
        $quote->setBody('body');

        $manager->persist($quote);
        $manager->flush();

        self::$quotes[] = $quote;
    }
}
