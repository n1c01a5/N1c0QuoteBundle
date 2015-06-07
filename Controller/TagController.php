<?php

namespace N1c0\QuoteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

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
use N1c0\QuoteBundle\Form\TagType;
use N1c0\QuoteBundle\Model\TagInterface;

class TagController extends FOSRestController
{
    /**
     * Get single Tag.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Tag for a given id",
     *   output = "N1c0\QuoteBundle\Entity\Tag",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the tag or the quote is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="tag")
     *
     * @param int                   $id                   the quote id
     * @param int                   $tagId           the tag id
     *
     * @return array
     *
     * @throws NotFoundHttpException when tag not exist
     */
    public function getTagAction($id, $tagId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->getOr404($tagId);
    }

    /**
     * Get the tags of a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing tags.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many tags to return.")
     *
     * @Annotations\View(
     *  templateVar="tags"
     * )
     *
     * @param int     $id                  the quote uuid
     *
     * @return array
     */
    public function getTagsAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->container->get('n1c0_quote.manager.tag')->findTagsByQuote($quote);
    }

    /**
     * Presents the form to use to create a new tag.
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
    public function newTagAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        $tag = $this->container->get('n1c0_quote.manager.tag')->createTag($quote);

        $form = $this->container->get('n1c0_quote.form_factory.tag')->createForm();
        $form->setData($tag);

        return array(
            'form' => $form,
            'id' => $id
        );
    }

    /**
     * Edits an tag.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tag:editTag.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id                       the quote id
     * @param int     $tagId           the tag id
     *
     * @return FormTypeInterface
     */
    public function editTagAction($id, $tagId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        $tag = $this->getOr404($tagId);

        $form = $this->container->get('n1c0_quote.form_factory.tag')->createForm();
        $form->setData($tag);

        return array(
            'form'           => $form,
            'id'             => $id,
            'tagId' => $tag->getId()
        );
    }


    /**
     * Creates a new Tag for the Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new tag for the quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\TagType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tag:newTag.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the quote
     *
     * @return FormTypeInterface|View
     */
    public function postTagAction(Request $request, $id)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $tagManager = $this->container->get('n1c0_quote.manager.tag');
            $tag = $tagManager->createTag($quote);

            $form = $this->container->get('n1c0_quote.form_factory.tag')->createForm();
            $form->setData($tag);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $tagManager->saveTag($tag);

                    $routeOptions = array(
                        'id'      => $id,
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;

                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) {
                        // Add a method onCreateTagSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_quote', $routeOptions, Codes::HTTP_CREATED);
                    }
                } else {
                    $response['success'] = false;
                    $response['form'] = $form->getErrorsAsString();
                }
                return new JsonResponse( $response );
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing tag from the submitted data or create a new tag at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\TagType",
     *   statusCodes = {
     *     201 = "Returned when the Tag is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tag:editQuoteTag.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote
     * @param int     $tagId      the tag id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when tag not exist
     */
    public function putTagAction(Request $request, $id, $tagId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $tag = $this->getOr404($tagId);

            $form = $this->container->get('n1c0_quote.form_factory.tag')->createForm();
            $form->setData($tag);
            $form->bind($request);

            if ($form->isValid()) {
                $tagManager = $this->container->get('n1c0_quote.manager.tag');
                if ($tagManager->saveTag($quote, $tag) !== false) {
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
     * Update existing tag for a quote from the submitted data or create a new tag at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\TagType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Tag:editQuoteTag.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote
     * @param int     $tagId      the tag id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when tag not exist
     */
    public function patchTagAction(Request $request, $id, $tagId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $tag = $this->getOr404($tagId);

            $form = $this->container->get('n1c0_quote.form_factory.tag')->createForm();
            $form->setData($tag);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $tagManager = $this->container->get('n1c0_quote.manager.tag');
                if ($tagManager->saveTag($quote, $tag) !== false) {
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
     * Get thread for an tag.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a tag thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the quote id
     * @param int     $tagId       the tag id
     *
     * @return array
     */
    public function getTagThreadAction($id, $tagId)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($tagId);
    }

    /**
     * Fetch a Tag or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return TagInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($tag = $this->container->get('n1c0_quote.manager.tag')->findTagById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $tag;
    }

    /**
     * Get download for the tag.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download tag",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="tag")
     *
     * @param int     $id                  the quote uuid
     * @param int     $tagId      the tag uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     */
    public function getTagDownloadAction($id, $tagId)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        if (!($tag = $this->container->get('n1c0_quote.manager.tag')->findTagById($tagId))) {
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
            'tagId'          => $tagId
        );
    }

    /**
     * Convert the tag in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the tag",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id                  the quote uuid
     * @param int     $tagId      the tag uuid
     * @param string  $format              the format to convert quote
     *
     * @return Response
     * @throws NotFoundHttpException when quote not exist
     */
    public function getTagConvertAction($id, $tagId, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The quote with the id \'%s\' was not found.',$id));
        }

        if (!($tag = $this->container->get('n1c0_quote.manager.tag')->findTagById($tagId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $tagConvert = $this->container->get('n1c0_quote.tag.download')->getConvert($tagId, $format);

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
        $filename = $tag->getTitle().'.'.$ext;
        $fh = fopen('./uploads/'.$filename, "w+");
        if($fh==false)
            die("Oops! Unable to create file");
        fputs($fh, $tagConvert);
        return $this->redirect($_SERVER['SCRIPT_NAME'].'/../uploads/'.$filename);
    }


}
