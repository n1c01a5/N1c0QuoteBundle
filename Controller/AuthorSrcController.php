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
use N1c0\QuoteBundle\Form\AuthorSrcType;
use N1c0\QuoteBundle\Model\AuthorSrcInterface;

class AuthorSrcController extends FOSRestController
{
    /**
     * Get single AuthorSrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a AuthorSrc for a given id",
     *   output = "N1c0\QuoteBundle\Entity\AuthorSrc",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the authorSrc or the quote is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="authorSrc")
     *
     * @param int                   $id                   the quote id
     * @param int                   $authorSrcId           the authorSrc id
     *
     * @return array
     *
     * @throws NotFoundHttpException when authorSrc not exist
     */
    public function getAuthorSrcAction($id, $authorSrcId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        
        return $this->getOr404($authorSrcId);
    }

    /**
     * Get the authorSrcs of a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing authorSrcs.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many authorSrcs to return.")
     *
     * @Annotations\View(
     *  templateVar="authorSrcs"
     * )
     *
     * @param int                   $id           the quote id
     *
     * @return array
     */
    public function getAuthorSrcsAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_quote.manager.authorSrc')->findAuthorSrcsByQuote($quote);
    }

    /**
     * Presents the form to use to create a new authorSrc.
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
    public function newAuthorSrcAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        $authorSrc = $this->container->get('n1c0_quote.manager.authorSrc')->createAuthorSrc($quote);

        $form = $this->container->get('n1c0_quote.form_factory.authorSrc')->createForm();
        $form->setData($authorSrc);

        return array(
            'form' => $form, 
            'id' => $id
        );
    }

    /**
     * Edits an authorSrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:AuthorSrc:editAuthorSrc.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id                       the quote id
     * @param int     $authorSrcId           the authorSrc id
     *
     * @return FormTypeInterface
     */
    public function editAuthorSrcAction($id, $authorSrcId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        $authorSrc = $this->getOr404($authorSrcId);

        $form = $this->container->get('n1c0_quote.form_factory.authorSrc')->createForm();
        $form->setData($authorSrc);
    
        return array(
            'form'           => $form,
            'id'             => $id,
            'authorSrcId' => $authorSrc->getId()
        );
    }


    /**
     * Creates a new AuthorSrc for the Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new authorSrc for the quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\AuthorSrcType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:AuthorSrc:newAuthorSrc.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the quote 
     *
     * @return FormTypeInterface|View
     */
    public function postAuthorSrcAction(Request $request, $id)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $authorSrcManager = $this->container->get('n1c0_quote.manager.authorSrc');
            $authorSrc = $authorSrcManager->createAuthorSrc($quote);

            $form = $this->container->get('n1c0_quote.form_factory.authorSrc')->createForm();
            $form->setData($authorSrc);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $authorSrcManager->saveAuthorSrc($authorSrc);
                
                    $routeOptions = array(
                        'id' => $id,
                        'authorSrcId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;
                    
                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) { 
                        // Add a method onCreateAuthorSrcSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_quote_authorSrc', $routeOptions, Codes::HTTP_CREATED);
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
     * Update existing authorSrc from the submitted data or create a new authorSrc at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\AuthorSrcType",
     *   statusCodes = {
     *     201 = "Returned when the AuthorSrc is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:AuthorSrc:editQuoteAuthorSrc.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $authorSrcId      the authorSrc id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when authorSrc not exist
     */
    public function putAuthorSrcAction(Request $request, $id, $authorSrcId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $authorSrc = $this->getOr404($authorSrcId);

            $form = $this->container->get('n1c0_quote.form_factory.authorSrc')->createForm();
            $form->setData($authorSrc);
            $form->bind($request);

            if ($form->isValid()) {
                $authorSrcManager = $this->container->get('n1c0_quote.manager.authorSrc');
                if ($authorSrcManager->saveAuthorSrc($authorSrc) !== false) {
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
    }

    /**
     * Update existing authorSrc for a quote from the submitted data or create a new authorSrc at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\AuthorSrcType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:AuthorSrc:editQuoteAuthorSrc.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $authorSrcId      the authorSrc id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when authorSrc not exist
     */
    public function patchAuthorSrcAction(Request $request, $id, $authorSrcId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $authorSrc = $this->getOr404($authorSrcId);

            $form = $this->container->get('n1c0_quote.form_factory.authorSrc')->createForm();
            $form->setData($authorSrc);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $authorSrcManager = $this->container->get('n1c0_quote.manager.authorSrc');
                if ($authorSrcManager->saveAuthorSrc($authorSrc) !== false) {
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
     * Get thread for an authorSrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a authorSrc thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the quote id
     * @param int     $authorSrcId       the authorSrc id
     *
     * @return array
     */
    public function getAuthorSrcThreadAction($id, $authorSrcId)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($authorSrcId);
    }

    /**
     * Fetch a AuthorSrc or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return AuthorSrcInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($authorSrc = $this->container->get('n1c0_quote.manager.authorSrc')->findAuthorSrcById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $authorSrc;
    }

    /**
     * Get download for the authorSrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download authorSrc",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="authorSrc")
     *
     * @param int     $id                  the quote uuid
     * @param int     $authorSrcId      the authorSrc uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     */
    public function getAuthorSrcDownloadAction($id, $authorSrcId)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        if (!($authorSrc = $this->container->get('n1c0_quote.manager.authorSrc')->findAuthorSrcById($authorSrcId))) {
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
            'formats'        => $formats, 
            'id'             => $id,
            'authorSrcId' => $authorSrcId
        );
    }

    /**
     * Convert the authorSrc in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the authorSrc",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id                  the quote uuid
     * @param int     $authorSrcId      the authorSrc uuid
     * @param string  $format              the format to convert quote 
     *
     * @return Response
     * @throws NotFoundHttpException when quote not exist
     */
    public function getAuthorSrcConvertAction($id, $authorSrcId, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The quote with the id \'%s\' was not found.',$id));
        }

        if (!($authorSrc = $this->container->get('n1c0_quote.manager.authorSrc')->findAuthorSrcById($authorSrcId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $authorSrcConvert = $this->container->get('n1c0_quote.authorSrc.download')->getConvert($authorSrcId, $format);

        $response = new Response();
        $response->setContent($authorSrcConvert);
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
   
        $response->headers->set('Content-disposition', 'filename='.$authorSrc->getTitle().'.'.$ext);
         
        return $response;
    }

}
