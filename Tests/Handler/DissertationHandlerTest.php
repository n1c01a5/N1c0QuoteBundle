<?php

namespace N1c0\QuoteBundle\Tests\Handler;

use N1c0\QuoteBundle\Handler\QuoteHandler;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Entity\Quote;

class QuoteHandlerTest extends \PHPUnit_Framework_TestCase
{
    const PAGE_CLASS = 'n1c0\QuoteBundle\Tests\Handler\DummyQuote';

    /** @var QuoteHandler */
    protected $quoteHandler;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }
        
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::PAGE_CLASS));
    }


    public function testGet()
    {
        $id = 1;
        $quote = $this->getQuote();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($quote));

        $this->quoteHandler = $this->createQuoteHandler($this->om, static::PAGE_CLASS,  $this->formFactory);

        $this->quoteHandler->get($id);
    }

    public function testAll()
    {
        $offset = 1;
        $limit = 2;

        $quotes = $this->getQuotes(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with(array(), null, $limit, $offset)
            ->will($this->returnValue($quotes));

        $this->quoteHandler = $this->createQuoteHandler($this->om, static::PAGE_CLASS,  $this->formFactory);

        $all = $this->quoteHandler->all($limit, $offset);

        $this->assertEquals($quotes, $all);
    }

    public function testPost()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $quote = $this->getQuote();
        $quote->setTitle($title);
        $quote->setBody($body);

        $form = $this->getMock('n1c0\QuoteBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($quote));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->quoteHandler = $this->createQuoteHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $quoteObject = $this->quoteHandler->post($parameters);

        $this->assertEquals($quoteObject, $quote);
    }

    /**
     * @expectedException n1c0\QuoteBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $quote = $this->getQuote();
        $quote->setTitle($title);
        $quote->setBody($body);

        $form = $this->getMock('n1c0\QuoteBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->quoteHandler = $this->createQuoteHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $this->quoteHandler->post($parameters);
    }

    public function testPut()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $quote = $this->getQuote();
        $quote->setTitle($title);
        $quote->setBody($body);

        $form = $this->getMock('n1c0\QuoteBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($quote));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->quoteHandler = $this->createQuoteHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $quoteObject = $this->quoteHandler->put($quote, $parameters);

        $this->assertEquals($quoteObject, $quote);
    }

    public function testPatch()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('body' => $body);

        $quote = $this->getQuote();
        $quote->setTitle($title);
        $quote->setBody($body);

        $form = $this->getMock('n1c0\QuoteBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($quote));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->quoteHandler = $this->createQuoteHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $quoteObject = $this->quoteHandler->patch($quote, $parameters);

        $this->assertEquals($quoteObject, $quote);
    }


    protected function createQuoteHandler($objectManager, $quoteClass, $formFactory)
    {
        return new QuoteHandler($objectManager, $quoteClass, $formFactory);
    }

    protected function getQuote()
    {
        $quoteClass = static::PAGE_CLASS;

        return new $quoteClass();
    }

    protected function getQuotes($maxQuotes = 5)
    {
        $quotes = array();
        for($i = 0; $i < $maxQuotes; $i++) {
            $quotes[] = $this->getQuote();
        }

        return $quotes;
    }
}

class DummyQuote extends Quote
{
}
