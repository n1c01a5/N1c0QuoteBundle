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
use N1c0\QuoteBundle\Form\TagsType;
use N1c0\QuoteBundle\Model\TagsInterface;

class TagsController extends FOSRestController
{
    /**
     * Get single Tags.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Tags for a given id",
     *   output = "N1c0\QuoteBundle\Entity\Tags",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the tags or the quote is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="tags")
     *
     * @param int                   $id                   the quote id
     * @param int                   $tagsId           the tags id
     *
     * @return array
     *
     * @throws NotFoundHttpException when tags not exist
     */
    public function getTagsAction($id, $tagsId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        
        return $this->getOr404($tagsId);
    }

    /**
     * Get the tagss of a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing tagss.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many tagss to return.")
     *
     * @Annotations\View(
     *  templateVar="tagss"
     * )
     *
     * @param int                   $id           the quote id
     *
     * @return array
     */
    public function getTagssAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_quote.manager.tags')->findTagssByQuote($quote);
    }

    /**
     * Presents the form to use to create a new tags.
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
    public function newTagsAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        $tags = $this->container->get('n1c0_quote.manager.tags')->createTags($quote);

        $form = $this->container->get('n1c0_quote.form_factory.tags')->createForm();
        $form->setData($tags);

        return array(
            'form' => $form, 
            'id' => $id
        );
    }

    /**
     * Edits an tags.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * 
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tags:editTags.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id                       the quote id
     * @param int     $tagsId           the tags id
     *
     * @return FormTypeInterface
     */
    public function editTagsAction($id, $tagsId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        $tags = $this->getOr404($tagsId);

        $form = $this->container->get('n1c0_quote.form_factory.tags')->createForm();
        $form->setData($tags);
    
        return array(
            'form'           => $form,
            'id'             => $id,
            'tagsId' => $tags->getId()
        );
    }


    /**
     * Creates a new Tags for the Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new tags for the quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\TagsType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tags:newTags.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the quote 
     *
     * @return FormTypeInterface|View
     */
    public function postTagsAction(Request $request, $id)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $tagsManager = $this->container->get('n1c0_quote.manager.tags');
            $tags = $tagsManager->createTags($quote);

            $form = $this->container->get('n1c0_quote.form_factory.tags')->createForm();
            $form->setData($tags);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $tagsManager->saveTags($tags);
                
                    $routeOptions = array(
                        'id' => $id,
                        'tagsId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;
                    
                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) { 
                        // Add a method onCreateTagsSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_quote_tags', $routeOptions, Codes::HTTP_CREATED);
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
     * Update existing tags from the submitted data or create a new tags at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\TagsType",
     *   statusCodes = {
     *     201 = "Returned when the Tags is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tags:editQuoteTags.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $tagsId      the tags id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when tags not exist
     */
    public function putTagsAction(Request $request, $id, $tagsId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $tags = $this->getOr404($tagsId);

            $form = $this->container->get('n1c0_quote.form_factory.tags')->createForm();
            $form->setData($tags);
            $form->bind($request);

            if ($form->isValid()) {
                $tagsManager = $this->container->get('n1c0_quote.manager.tags');
                if ($tagsManager->saveTags($tags) !== false) {
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
     * Update existing tags for a quote from the submitted data or create a new tags at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\TagsType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tags:editQuoteTags.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote 
     * @param int     $tagsId      the tags id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when tags not exist
     */
    public function patchTagsAction(Request $request, $id, $tagsId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $tags = $this->getOr404($tagsId);

            $form = $this->container->get('n1c0_quote.form_factory.tags')->createForm();
            $form->setData($tags);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $tagsManager = $this->container->get('n1c0_quote.manager.tags');
                if ($tagsManager->saveTags($tags) !== false) {
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
     * Get thread for an tags.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a tags thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the quote id
     * @param int     $tagsId       the tags id
     *
     * @return array
     */
    public function getTagsThreadAction($id, $tagsId)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($tagsId);
    }

    /**
     * Fetch a Tags or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return TagsInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($tags = $this->container->get('n1c0_quote.manager.tags')->findTagsById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $tags;
    }

    /**
     * Get download for the tags.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download tags",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="tags")
     *
     * @param int     $id                  the quote uuid
     * @param int     $tagsId      the tags uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     */
    public function getTagsDownloadAction($id, $tagsId)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        if (!($tags = $this->container->get('n1c0_quote.manager.tags')->findTagsById($tagsId))) {
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
            'tagsId' => $tagsId
        );
    }

    /**
     * Convert the tags in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the tags",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id                  the quote uuid
     * @param int     $tagsId      the tags uuid
     * @param string  $format              the format to convert quote 
     *
     * @return Response
     * @throws NotFoundHttpException when quote not exist
     */
    public function getTagsConvertAction($id, $tagsId, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The quote with the id \'%s\' was not found.',$id));
        }

        if (!($tags = $this->container->get('n1c0_quote.manager.tags')->findTagsById($tagsId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $tagsConvert = $this->container->get('n1c0_quote.tags.download')->getConvert($tagsId, $format);

        $response = new Response();
        $response->setContent($tagsConvert);
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
   
        $response->headers->set('Content-disposition', 'filename='.$tags->getTitle().'.'.$ext);
         
        return $response;
    }

}
