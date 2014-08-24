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

class QuoteController extends FOSRestController
{
    /**
     * List all quotes.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing quotes.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="100", description="How many quotes to return.")
     * @Annotations\QueryParam(name="definition", requirements="\d", default="1", description="Is it a definition?")
     *
     * @Annotations\View(
     *  templateVar="quotes"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getQuotesAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');
        
        $criteria = $paramFetcher->get('definition');

        return $this->container->get('n1c0_quote.manager.quote')->by(array('definition' => $criteria), $limit, $offset);
    }

    /**
     * Get single Quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Quote for a given id",
     *   output = "N1c0\QuoteBundle\Entity\Quote",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the quote is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="quote")
     *
     * @param int     $id      the quote id
     *
     * @return array
     *
     * @throws NotFoundHttpException when quote not exist
     */
    public function getQuoteAction($id)
    {
        $quote = $this->getOr404($id);

        return $quote;
    }

    /**
     * Presents the form to use to create a new quote.
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
     * @return FormTypeInterface
     */
    public function newQuoteAction()
    {
        return $form = $this->container->get('n1c0_quote.form_factory.quote')->createForm();
    }

    /**
     * Edits a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Quote:editQuote.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id      the quote id
     * @return FormTypeInterface
     */
    public function editQuoteAction($id)
    {
        $quote = $this->getOr404($id);
        $form = $this->container->get('n1c0_quote.form_factory.quote')->createForm();
        $form->setData($quote);
    
        return array(
            'form' => $form, 
            'id'=>$id
        );
    }

    /**
     * Create a Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\QuoteType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Quote:newQuote.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postQuoteAction(Request $request)
    {
        try {
            $quoteManager = $this->container->get('n1c0_quote.manager.quote');
            $quote = $quoteManager->createQuote();

            $form = $this->container->get('n1c0_quote.form_factory.quote')->createForm();
            $form->setData($quote);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $quoteManager->saveQuote($quote);
                
                    $routeOptions = array(
                        'id' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    // Add a method onCreateQuoteSuccess(FormInterface $form)
                    return $this->routeRedirectView('api_1_get_quote', $routeOptions, Codes::HTTP_CREATED);
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        // Add a method onCreateQuoteError(FormInterface $form)
        return new Response(sprintf("Error of the quote id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);

    }

    /**
     * Update existing quote from the submitted data or create a new quote at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates a quote.",
     *   input = "N1c0\DemoBundle\Form\QuoteType",
     *   statusCodes = {
     *     200 = "Returned when the Quote is updated",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Quote:editQuote.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the quote id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when quote not exist
     */
    public function putQuoteAction(Request $request, $id)
    {
        try {
            $quote = $this->getOr404($id);

            $form = $this->container->get('n1c0_quote.form_factory.quote')->createForm();
            $form->setData($quote);
            $form->bind($request);

            if ($form->isValid()) {
                $quoteManager = $this->container->get('n1c0_quote.manager.quote');
                if($quoteManager->saveQuote($quote) !== false) {
                    $routeOptions = array(
                        'id' => $quote->getId(),
                        '_format' => $request->get('_format')
                    );

                    return $this->routeRedirectView('api_1_get_quote', $routeOptions, Codes::HTTP_OK); // Must return 200 for ajax request
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        // Add a method onCreateQuoteError(FormInterface $form)
        return new Response(sprintf("Error of the quote id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Update existing quote from the submitted data or create a new quote at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates a quote.",
     *   input = "N1c0\DemoBundle\Form\QuoteType",
     *   statusCodes = {
     *     200 = "Returned when the Quote is updated",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Quote:editQuote.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the quote id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when quote not exist
     */
    public function patchQuoteAction(Request $request, $id)
    {
        try {
            $quote = $this->getOr404($id);

            $form = $this->container->get('n1c0_quote.form_factory.quote')->createForm();
            $form->setData($quote);
            $form->bind($request);

            if ($form->isValid()) {
                $quoteManager = $this->container->get('n1c0_quote.manager.quote');
                if($quoteManager->saveQuote($quote) !== false) {
                    $routeOptions = array(
                        'id' => $quote->getId(),
                        '_format' => $request->get('_format')
                    );

                    return $this->routeRedirectView('api_1_get_quote', $routeOptions, Codes::HTTP_OK); // Must return 200 for ajax request
                }
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        // Add a method onCreateQuoteError(FormInterface $form)
        return new Response(sprintf("Error of the quote id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Get thread for the quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a comment thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id      the quote uuid
     *
     * @return array
     */
    public function getQuoteThreadAction($id)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($id);
    }

    /**
     * Fetch a Quote or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return QuoteInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $quote;
    }

    /**
     * Get download for the quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download quote",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="quote")
     *
     * @param int     $id      the quote uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     */
    public function getQuoteDownloadAction($id)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
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
            'formats' => $formats,
            'id' => $id
        );
    }

    /**
     * Convert the quote in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the quote",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id      the quote uuid
     * @param string  $format  the format to convert quote 
     *
     * @return Response
     * @throws NotFoundHttpException when quote not exist
     */
    public function getQuoteConvertAction($id, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $quoteConvert = $this->container->get('n1c0_quote.quote.download')->getConvert($id, $format);

        $response = new Response();
        $response->setContent($quoteConvert);
        $response->headers->set('Content-Type', 'application/force-download');
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
        $response->headers->set('Content-disposition', 'filename='.$quote->getTitle().'.'.$ext);
         
        return $response;
    }
    
    /**
     * Get logs of a single Quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets lofs of a Quote for a given id",
     *   output = "Gedmo\Loggable\Entity\LogEntry",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the quote is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="quote")
     *
     * @param int     $id      the quote id
     *
     * @return array
     *
     * @throws NotFoundHttpException when quote not exist
     */
    public function logsQuoteAction($id)
    {
        $quote = $this->getOr404($id);
        $repo = $em->getRepository('Gedmo\Loggable\Entity\LogEntry'); // we use default log entry class
        $entity = $em->find('Entity\Quote', $quote->getId());
        $logs = $repo->getLogEntries($entity);
        
        return $logs;
    }
}
