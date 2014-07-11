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
use N1c0\QuoteBundle\Form\HousePublishingType;
use N1c0\QuoteBundle\Model\HousePublishingInterface;

class HousePublishingController extends FOSRestController
{
    /**
     * Get single HousePublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a HousePublishing for a given id",
     *   output = "N1c0\QuoteBundle\Entity\HousePublishing",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the housePublishing or the quote is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="housePublishing")
     *
     * @param int                   $id                   the quote id
     * @param int                   $housePublishingId           the housePublishing id
     *
     * @return array
     *
     * @throws NotFoundHttpException when housePublishing not exist
     */
    public function getHousePublishingAction($id, $housePublishingId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        
        return $this->getOr404($housePublishingId);
    }

    /**
     * Get the housePublishings of a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing housePublishings.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many housePublishings to return.")
     *
     * @Annotations\View(
     *  templateVar="housePublishings"
     * )
     *
     * @param int                   $id           the quote id
     *
     * @return array
     */
    public function getHousePublishingsAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_quote.manager.housePublishing')->findHousePublishingsByQuote($quote);
    }

    /**
     * Presents the form to use to create a new housePublishing.
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
    public function newHousePublishingAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        $housePublishing = $this->container->get('n1c0_quote.manager.housePublishing')->createHousePublishing($quote);

        $form = $this->container->get('n1c0_quote.form_factory.housePublishing')->createForm();
        $form->setData($housePublishing);

        return array(
            'form' => $form, 
            'id' => $id
        );
    }

    /**
     * Edits an housePublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:HousePublishing:editHousePublishing.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id                       the quote id
     * @param int     $housePublishingId           the housePublishing id
     *
     * @return FormTypeInterface
     */
    public function editHousePublishingAction($id, $housePublishingId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        $housePublishing = $this->getOr404($housePublishingId);

        $form = $this->container->get('n1c0_quote.form_factory.housePublishing')->createForm();
        $form->setData($housePublishing);
    
        return array(
            'form'           => $form,
            'id'             => $id,
            'housePublishingId' => $housePublishing->getId()
        );
    }


    /**
     * Creates a new HousePublishing for the Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new housePublishing for the quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\HousePublishingType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:HousePublishing:newHousePublishing.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the quote 
     *
     * @return FormTypeInterface|View
     */
    public function postHousePublishingAction(Request $request, $id)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $housePublishingManager = $this->container->get('n1c0_quote.manager.housePublishing');
            $housePublishing = $housePublishingManager->createHousePublishing($quote);

            $form = $this->container->get('n1c0_quote.form_factory.housePublishing')->createForm();
            $form->setData($housePublishing);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $housePublishingManager->saveHousePublishing($housePublishing);
                
                    $routeOptions = array(
                        'id' => $id,
                        'housePublishingId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;
                    
                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) { 
                        // Add a method onCreateHousePublishingSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_quote_housePublishing', $routeOptions, Codes::HTTP_CREATED);
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
     * Update existing housePublishing from the submitted data or create a new housePublishing at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\HousePublishingType",
     *   statusCodes = {
     *     201 = "Returned when the HousePublishing is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:HousePublishing:editQuoteHousePublishing.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $housePublishingId      the housePublishing id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when housePublishing not exist
     */
    public function putHousePublishingAction(Request $request, $id, $housePublishingId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $housePublishing = $this->getOr404($housePublishingId);

            $form = $this->container->get('n1c0_quote.form_factory.housePublishing')->createForm();
            $form->setData($housePublishing);
            $form->bind($request);

            if ($form->isValid()) {
                $housePublishingManager = $this->container->get('n1c0_quote.manager.housePublishing');
                if ($housePublishingManager->saveHousePublishing($housePublishing) !== false) {
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
     * Update existing housePublishing for a quote from the submitted data or create a new housePublishing at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\HousePublishingType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:HousePublishing:editQuoteHousePublishing.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $housePublishingId      the housePublishing id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when housePublishing not exist
     */
    public function patchHousePublishingAction(Request $request, $id, $housePublishingId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $housePublishing = $this->getOr404($housePublishingId);

            $form = $this->container->get('n1c0_quote.form_factory.housePublishing')->createForm();
            $form->setData($housePublishing);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $housePublishingManager = $this->container->get('n1c0_quote.manager.housePublishing');
                if ($housePublishingManager->saveHousePublishing($housePublishing) !== false) {
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
     * Get thread for an housePublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a housePublishing thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the quote id
     * @param int     $housePublishingId       the housePublishing id
     *
     * @return array
     */
    public function getHousePublishingThreadAction($id, $housePublishingId)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($housePublishingId);
    }

    /**
     * Fetch a HousePublishing or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return HousePublishingInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($housePublishing = $this->container->get('n1c0_quote.manager.housePublishing')->findHousePublishingById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $housePublishing;
    }

    /**
     * Get download for the housePublishing.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download housePublishing",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="housePublishing")
     *
     * @param int     $id                  the quote uuid
     * @param int     $housePublishingId      the housePublishing uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     */
    public function getHousePublishingDownloadAction($id, $housePublishingId)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        if (!($housePublishing = $this->container->get('n1c0_quote.manager.housePublishing')->findHousePublishingById($housePublishingId))) {
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
            'housePublishingId' => $housePublishingId
        );
    }

    /**
     * Convert the housePublishing in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the housePublishing",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id                  the quote uuid
     * @param int     $housePublishingId      the housePublishing uuid
     * @param string  $format              the format to convert quote 
     *
     * @return Response
     * @throws NotFoundHttpException when quote not exist
     */
    public function getHousePublishingConvertAction($id, $housePublishingId, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The quote with the id \'%s\' was not found.',$id));
        }

        if (!($housePublishing = $this->container->get('n1c0_quote.manager.housePublishing')->findHousePublishingById($housePublishingId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $housePublishingConvert = $this->container->get('n1c0_quote.housePublishing.download')->getConvert($housePublishingId, $format);

        $response = new Response();
        $response->setContent($housePublishingConvert);
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
   
        $response->headers->set('Content-disposition', 'filename='.$housePublishing->getTitle().'.'.$ext);
         
        return $response;
    }

}
