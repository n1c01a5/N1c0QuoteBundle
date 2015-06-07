<?php

namespace N1c0\QuoteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use N1c0\QuoteBundle\Exception\InvalidFormException;
use N1c0\QuoteBundle\Form\QuoteType;
use N1c0\QuoteBundle\Model\QuoteInterface;
use N1c0\QuoteBundle\Form\BookType;
use N1c0\QuoteBundle\Model\BookInterface;

class BookController extends FOSRestController
{
    /**
     * Get single Book.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Book for a given id",
     *   output = "N1c0\QuoteBundle\Entity\Book",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the book or the quote is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="book")
     *
     * @param int                   $id                   the quote id
     * @param int                   $bookId           the book id
     *
     * @return array
     *
     * @throws NotFoundHttpException when book not exist
     */
    public function getBookAction($id, $bookId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->getOr404($bookId);
    }

    /**
     * Get the books of a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing books.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many books to return.")
     *
     * @Annotations\View(
     *  templateVar="books"
     * )
     *
     * @param int                   $id           the quote id
     *
     * @return array
     */
    public function getBooksAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_quote.manager.book')->findBooksByQuote($quote);
    }

    /**
     * Presents the form to use to create a new book.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  templateVar = "form"
     * )
     *
     * @param int                   $id           the quote id
     *
     * @return FormTypeInterface
     */
    public function newBookAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        $book = $this->container->get('n1c0_quote.manager.book')->createBook($quote);

        $form = $this->container->get('n1c0_quote.form_factory.book')->createForm();
        $form->setData($book);

        return array(
            'form' => $form,
            'id' => $id
        );
    }

    /**
     * Edits an book.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Book:editBook.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id      the quote id
     * @param int     $bookId           the book id
     *
     * @return FormTypeInterface
     */
    public function editBookAction($id, $bookId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        $book = $this->getOr404($bookId);
        $form = $this->container->get('n1c0_quote.form_factory.book')->createForm();
        $form->setData($book);

        return array(
            'form'         => $form,
            'id'           =>$id,
            'bookId' => $book->getId()
        );
    }

    /**
     * Creates a new Book for the Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new book for the quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\BookType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Book:newBook.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the quote
     *
     * @return FormTypeInterface|View
     */
    public function postBookAction(Request $request, $id)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $bookManager = $this->container->get('n1c0_quote.manager.book');
            $book = $bookManager->createBook($quote);

            $form = $this->container->get('n1c0_quote.form_factory.book')->createForm();
            $form->setData($book);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $bookManager->saveBook($book);

                    $routeOptions = array(
                        'id' => $id,
                        'bookId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;

                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) {
                        // Add a method onCreateBookSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_quote_book', $routeOptions, Codes::HTTP_CREATED);
                    }
                } else {
                    $response['success'] = false;
                }
                return new JsonResponse( $response );
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing book from the submitted data or create a new book at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\BookType",
     *   statusCodes = {
     *     201 = "Returned when the Book is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Book:editBook.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote
     * @param int     $bookId      the book id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when book not exist
     */
    public function putBookAction(Request $request, $id, $bookId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $book = $this->getOr404($bookId);

            $form = $this->container->get('n1c0_quote.form_factory.book')->createForm();
            $form->setData($book);
            $form->bind($request);

            if ($form->isValid()) {
                $bookManager = $this->container->get('n1c0_quote.manager.book');
                if ($bookManager->saveBook($book) !== false) {
                    $routeOptions = array(
                        'id' => $quote->getId(),
                        '_format' => $request->get('_format')
                    );

                    return $this->routeRedirectView('api_1_get_quote', $routeOptions, Codes::HTTP_OK);
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        // Add a method onCreateBookError(FormInterface $form)
        return new Response(sprintf("Error of the book id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Update existing book for a quote from the submitted data or create a new book at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\BookType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Book:editQuoteBook.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote
     * @param int     $bookId      the book id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when book not exist
     */
    public function patchBookAction(Request $request, $id, $bookId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $book = $this->getOr404($bookId);

            $form = $this->container->get('n1c0_quote.form_factory.book')->createForm();
            $form->setData($book);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $bookManager = $this->container->get('n1c0_quote.manager.book');
                if ($bookManager->saveBook($book) !== false) {
                    $routeOptions = array(
                        'id' => $quote->getId(),
                        '_format' => $request->get('_format')
                    );

                    return $this->routeRedirectView('api_1_get_quote', $routeOptions, Codes::HTTP_CREATED);
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Get thread for an book.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a book thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the quote id
     * @param int     $bookId       the book id
     *
     * @return array
     */
    public function getBookThreadAction($id, $bookId)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($bookId);
    }

    /**
     * Fetch a Book or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return BookInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($book = $this->container->get('n1c0_quote.manager.book')->findBookById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $book;
    }

    /**
     * Get download for the book of the quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download book",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="book")
     *
     * @param int     $id              the quote uuid
     * @param int     $bookId      the book uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     * @throws NotFoundHttpException when book not exist
     */
    public function getBookDownloadAction($id, $bookId)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource quote \'%s\' was not found.',$id));
        }

        if (!($book = $this->container->get('n1c0_quote.manager.book')->findBookById($bookId))) {
            throw new NotFoundHttpException(sprintf('The resource book \'%s\' was not found.', $bookId));
        }

        $formats = array(
            "native",
            "json",
            "docx",
            "odt",
            "epub",
            "epub3",
            "fb2",
            "html",
            "html5",
            "slidy",
            "dzslides",
            "docbook",
            "opendocument",
            "latex",
            "beamer",
            "context",
            "texinfo",
            "markdown",
            "pdf",
            "plain",
            "rst",
            "mediawiki",
            "textile",
            "rtf",
            "org",
            "asciidoc"
        );

        return array(
            'formats'    => $formats,
            'book'   => $book
        );
    }

    /**
     * Convert the book in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the book",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id              the quote uuid
     * @param int     $bookId      the book uuid
     * @param string  $format          the format to convert quote
     *
     * @return null
     * @throws NotFoundHttpException when quote not exist
     * @throws NotFoundHttpException when book not exist
     */
    public function getBookConvertAction($id, $bookId, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource quote \'%s\' was not found.',$id));
        }

        if (!($book = $this->container->get('n1c0_quote.manager.book')->findBookById($bookId))) {
            throw new NotFoundHttpException(sprintf('The resource book \'%s\' was not found.',$bookId));
        }

        $bookConvert = $this->container->get('n1c0_quote.book.download')->getConvert($bookId, $format);

        switch ($format) {
            case "native":
                $ext = "";
            break;
            case "s5":
                $ext = "html";
            break;
            case "slidy":
                $ext = "html";
            break;
            case "slideous":
                $ext = "html";
            break;
            case "dzslides":
                $ext = "html";
            break;
            case "latex":
                $ext = "tex";
            break;
            case "context":
                $ext = "tex";
            break;
            case "beamer":
                $ext = "pdf";
            break;
            case "rst":
                $ext = "text";
            break;
            case "docbook":
                $ext = "db";
            break;
            case "man":
                $ext = "";
            break;
            case "asciidoc":
                $ext = "txt";
            break;
            case "markdown":
                $ext = "md";
            break;
            case "epub3":
                $ext = "epub";
            break;
            default:
                $ext = $format;
        }

        if ($ext == "") {$ext = "txt";}
        $filename = $book->getTitle().'.'.$ext;
        $fh = fopen('./uploads/'.$filename, "w+");
        if($fh==false)
            die("Oops! Unable to create file");
        fputs($fh, $bookConvert);

        return $this->redirect($_SERVER['SCRIPT_NAME'].'/../uploads/'.$filename);
    }

}
