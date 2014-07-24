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
use N1c0\QuoteBundle\Form\HousepublishingType;
use N1c0\QuoteBundle\Model\HousepublishingInterface;

class HousepublishingController extends FOSRestController
{
    /**
     * Get single Housepublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Housepublishing for a given id",
     *   output = "N1c0\QuoteBundle\Entity\Housepublishing",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the housepublishing or the quote is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="housepublishing")
     *
     * @param int                   $id                   the quote id
     * @param int                   $housepublishingId           the housepublishing id
     *
     * @return array
     *
     * @throws NotFoundHttpException when housepublishing not exist
     */
    public function getHousepublishingAction($id, $housepublishingId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        
        return $this->getOr404($housepublishingId);
    }

    /**
     * Get the housepublishings of a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing housepublishings.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many housepublishings to return.")
     *
     * @Annotations\View(
     *  templateVar="housepublishings"
     * )
     *
     * @param int                   $id           the quote id
     *
     * @return array
     */
    public function getHousepublishingsAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_quote.manager.housepublishing')->findHousepublishingsByQuote($quote);
    }

    /**
     * Presents the form to use to create a new housepublishing.
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
    public function newHousepublishingAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        $housepublishing = $this->container->get('n1c0_quote.manager.housepublishing')->createHousepublishing($quote);

        $form = $this->container->get('n1c0_quote.form_factory.housepublishing')->createForm();
        $form->setData($housepublishing);

        return array(
            'form' => $form, 
            'id' => $id
        );
    }

    /**
     * Edits an housepublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Housepublishing:editHousepublishing.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id                       the quote id
     * @param int     $housepublishingId           the housepublishing id
     *
     * @return FormTypeInterface
     */
    public function editHousepublishingAction($id, $housepublishingId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        $housepublishing = $this->getOr404($housepublishingId);

        $form = $this->container->get('n1c0_quote.form_factory.housepublishing')->createForm();
        $form->setData($housepublishing);
    
        return array(
            'form'           => $form,
            'id'             => $id,
            'housepublishingId' => $housepublishing->getId()
        );
    }


    /**
     * Creates a new Housepublishing for the Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new housepublishing for the quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\HousepublishingType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Housepublishing:newHousepublishing.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the quote 
     *
     * @return FormTypeInterface|View
     */
    public function postHousepublishingAction(Request $request, $id)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $housepublishingManager = $this->container->get('n1c0_quote.manager.housepublishing');
            $housepublishing = $housepublishingManager->createHousepublishing($quote);

            $form = $this->container->get('n1c0_quote.form_factory.housepublishing')->createForm();
            $form->setData($housepublishing);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $housepublishingManager->saveHousepublishing($housepublishing);
                
                    $routeOptions = array(
                        'id' => $id,
                        'housepublishingId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;
                    
                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) { 
                        // Add a method onCreateHousepublishingSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_quote_housepublishing', $routeOptions, Codes::HTTP_CREATED);
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
     * Update existing housepublishing from the submitted data or create a new housepublishing at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\HousepublishingType",
     *   statusCodes = {
     *     201 = "Returned when the Housepublishing is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Housepublishing:editQuoteHousepublishing.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $housepublishingId      the housepublishing id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when housepublishing not exist
     */
    public function putHousepublishingAction(Request $request, $id, $housepublishingId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $housepublishing = $this->getOr404($housepublishingId);

            $form = $this->container->get('n1c0_quote.form_factory.housepublishing')->createForm();
            $form->setData($housepublishing);
            $form->bind($request);

            if ($form->isValid()) {
                $housepublishingManager = $this->container->get('n1c0_quote.manager.housepublishing');
                if ($housepublishingManager->saveHousepublishing($housepublishing) !== false) {
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
     * Update existing housepublishing for a quote from the submitted data or create a new housepublishing at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\HousepublishingType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Housepublishing:editQuoteHousepublishing.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $housepublishingId      the housepublishing id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when housepublishing not exist
     */
    public function patchHousepublishingAction(Request $request, $id, $housepublishingId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $housepublishing = $this->getOr404($housepublishingId);

            $form = $this->container->get('n1c0_quote.form_factory.housepublishing')->createForm();
            $form->setData($housepublishing);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $housepublishingManager = $this->container->get('n1c0_quote.manager.housepublishing');
                if ($housepublishingManager->saveHousepublishing($housepublishing) !== false) {
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
     * Get thread for an housepublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a housepublishing thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the quote id
     * @param int     $housepublishingId       the housepublishing id
     *
     * @return array
     */
    public function getHousepublishingThreadAction($id, $housepublishingId)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($housepublishingId);
    }

    /**
     * Fetch a Housepublishing or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return HousepublishingInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($housepublishing = $this->container->get('n1c0_quote.manager.housepublishing')->findHousepublishingById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $housepublishing;
    }

    /**
     * Get download for the housepublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download housepublishing",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="housepublishing")
     *
     * @param int     $id                  the quote uuid
     * @param int     $housepublishingId      the housepublishing uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     */
    public function getHousepublishingDownloadAction($id, $housepublishingId)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        if (!($housepublishing = $this->container->get('n1c0_quote.manager.housepublishing')->findHousepublishingById($housepublishingId))) {
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
            'housepublishingId' => $housepublishingId
        );
    }

    /**
     * Convert the housepublishing in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the housepublishing",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id                  the quote uuid
     * @param int     $housepublishingId      the housepublishing uuid
     * @param string  $format              the format to convert quote 
     *
     * @return Response
     * @throws NotFoundHttpException when quote not exist
     */
    public function getHousepublishingConvertAction($id, $housepublishingId, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The quote with the id \'%s\' was not found.',$id));
        }

        if (!($housepublishing = $this->container->get('n1c0_quote.manager.housepublishing')->findHousepublishingById($housepublishingId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $housepublishingConvert = $this->container->get('n1c0_quote.housepublishing.download')->getConvert($housepublishingId, $format);

        $response = new Response();
        $response->setContent($housepublishingConvert);
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
   
        $response->headers->set('Content-disposition', 'filename='.$housepublishing->getTitle().'.'.$ext);
         
        return $response;
    }

}
