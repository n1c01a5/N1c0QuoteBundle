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
use N1c0\QuoteBundle\Form\AuthorsrcType;
use N1c0\QuoteBundle\Model\AuthorsrcInterface;

class AuthorsrcController extends FOSRestController
{
    /**
     * Get single Authorsrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Authorsrc for a given id",
     *   output = "N1c0\QuoteBundle\Entity\Authorsrc",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the authorsrc or the quote is not found"
     *   }
     * )
     *
     *
     * @Annotations\View(templateVar="authorsrc")
     *
     * @param int                   $id                   the quote id
     * @param int                   $authorsrcId           the authorsrc id
     *
     * @return array
     *
     * @throws NotFoundHttpException when authorsrc not exist
     */
    public function getAuthorsrcAction($id, $authorsrcId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $this->getOr404($authorsrcId);
    }

    /**
     * Get the authorsrcs of a quote.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing authorsrcs.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many authorsrcs to return.")
     *
     * @Annotations\View(
     *  templateVar="authorsrc"
     * )
     *
     * @param int                   $id           the quote id
     *
     * @return array
     */
    public function getAuthorsrcsAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        return $quote->getAuthorsrc();
    }

    /**
     * Presents the form to use to create a new authorsrc.
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
    public function newAuthorsrcAction($id)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }

        $authorsrc = $this->container->get('n1c0_quote.manager.authorsrc')->createAuthorsrc($quote);

        $form = $this->container->get('n1c0_quote.form_factory.authorsrc')->createForm();
        $form->setData($authorsrc);

        return array(
            'form' => $form,
            'id' => $id
        );
    }

    /**
     * Edits an authorsrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Authorsrc:editAuthorsrc.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param int     $id                       the quote id
     * @param int     $authorsrcId           the authorsrc id
     *
     * @return FormTypeInterface
     */
    public function editAuthorsrcAction($id, $authorsrcId)
    {
        $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
        if (!$quote) {
            throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
        }
        $authorsrc = $this->getOr404($authorsrcId);

        $form = $this->container->get('n1c0_quote.form_factory.authorsrc')->createForm();
        $form->setData($authorsrc);

        return array(
            'form'           => $form,
            'id'             => $id,
            'authorsrcId' => $authorsrc->getId()
        );
    }


    /**
     * Creates a new Authorsrc for the Quote from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new authorsrc for the quote from the submitted data.",
     *   input = "N1c0\QuoteBundle\Form\AuthorsrcType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Authorsrc:newAuthorsrc.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param string  $id      The id of the quote
     *
     * @return FormTypeInterface|View
     */
    public function postAuthorsrcAction(Request $request, $id)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $authorsrcManager = $this->container->get('n1c0_quote.manager.authorsrc');
            $authorsrc = $authorsrcManager->createAuthorsrc($quote);

            $form = $this->container->get('n1c0_quote.form_factory.authorsrc')->createForm();
            $form->setData($authorsrc);

            if ('POST' === $request->getMethod()) {
                $form->bind($request);

                if ($form->isValid()) {
                    $authorsrcManager->saveAuthorsrc($authorsrc);

                    $routeOptions = array(
                        'id' => $id,
                        'authorsrcId' => $form->getData()->getId(),
                        '_format' => $request->get('_format')
                    );

                    $response['success'] = true;

                    $request = $this->container->get('request');
                    $isAjax = $request->isXmlHttpRequest();

                    if($isAjax == false) {
                        // Add a method onCreateAuthorsrcSuccess(FormInterface $form)
                        return $this->routeRedirectView('api_1_get_quote_authorsrc', $routeOptions, Codes::HTTP_CREATED);
                    }
                } else {
                    $response['success'] = false;
                }
                return new JsonResponse($response);
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update existing authorsrc from the submitted data or create a new authorsrc at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\AuthorsrcType",
     *   statusCodes = {
     *     201 = "Returned when the Authorsrc is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Authorsrc:editQuoteAuthorsrc.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote
     * @param int     $authorsrcId      the authorsrc id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when authorsrc not exist
     */
    public function putAuthorsrcAction(Request $request, $id, $authorsrcId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $authorsrc = $this->getOr404($authorsrcId);

            $form = $this->container->get('n1c0_quote.form_factory.authorsrc')->createForm();
            $form->setData($authorsrc);
            $form->bind($request);

            if ($form->isValid()) {
                $authorsrcManager = $this->container->get('n1c0_quote.manager.authorsrc');
                if ($authorsrcManager->saveAuthorsrc($authorsrc) !== false) {
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
     * Update existing authorsrc for a quote from the submitted data or create a new authorsrc at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "N1c0\DemoBundle\Form\AuthorsrcType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "N1c0QuoteBundle:Authorsrc:editQuoteAuthorsrc.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request         the request object
     * @param string  $id              the id of the quote
     * @param int     $authorsrcId      the authorsrc id

     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when authorsrc not exist
     */
    public function patchAuthorsrcAction(Request $request, $id, $authorsrcId)
    {
        try {
            $quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id);
            if (!$quote) {
                throw new NotFoundHttpException(sprintf('Quote with identifier of "%s" does not exist', $id));
            }

            $authorsrc = $this->getOr404($authorsrcId);

            $form = $this->container->get('n1c0_quote.form_factory.authorsrc')->createForm();
            $form->setData($authorsrc);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $authorsrcManager = $this->container->get('n1c0_quote.manager.authorsrc');
                if ($authorsrcManager->saveAuthorsrc($authorsrc) !== false) {
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
     * Get thread for an authorsrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a authorsrc thread",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="thread")
     *
     * @param int     $id               the quote id
     * @param int     $authorsrcId       the authorsrc id
     *
     * @return array
     */
    public function getAuthorsrcThreadAction($id, $authorsrcId)
    {
        return $this->container->get('n1c0_quote.comment.quote_comment.default')->getThread($authorsrcId);
    }

    /**
     * Fetch a Authorsrc or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return AuthorsrcInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($authorsrc = $this->container->get('n1c0_quote.manager.authorsrc')->findAuthorsrcById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $authorsrc;
    }

    /**
     * Get download for the authorsrc.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a download authorsrc",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\View(templateVar="authorsrc")
     *
     * @param int     $id                  the quote uuid
     * @param int     $authorsrcId      the authorsrc uuid
     *
     * @return array
     * @throws NotFoundHttpException when quote not exist
     */
    public function getAuthorsrcDownloadAction($id, $authorsrcId)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        if (!($authorsrc = $this->container->get('n1c0_quote.manager.authorsrc')->findAuthorsrcById($authorsrcId))) {
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
            'authorsrcId' => $authorsrcId
        );
    }

    /**
     * Convert the authorsrc in pdf format.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Convert the authorsrc",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param int     $id                  the quote uuid
     * @param int     $authorsrcId      the authorsrc uuid
     * @param string  $format              the format to convert quote
     *
     * @return null
     * @throws NotFoundHttpException when quote not exist
     */
    public function getAuthorsrcConvertAction($id, $authorsrcId, $format)
    {
        if (!($quote = $this->container->get('n1c0_quote.manager.quote')->findQuoteById($id))) {
            throw new NotFoundHttpException(sprintf('The quote with the id \'%s\' was not found.',$id));
        }

        if (!($authorsrc = $this->container->get('n1c0_quote.manager.authorsrc')->findAuthorsrcById($authorsrcId))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        $authorsrcConvert = $this->container->get('n1c0_quote.authorsrc.download')->getConvert($authorsrcId, $format);

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

        if (!empty($authorsrc->getFirstName())) {
            $filename = $authorsrc->getName() . '_' . $authorsrc->getFirstName();
        } else {
            $filename = $authorsrc->getName();
        }

        $filename = $this->clean($filename) . '.' . $ext;

        $fh = fopen('./uploads/' . $filename, "w+");
        if($fh == false)
            die("Oops! Unable to create file");
        fputs($fh, $authorsrcConvert);

        return $this->redirect($_SERVER['SCRIPT_NAME'].'/../uploads/'.$filename);
    }

    private function clean($string) {
        $string = str_replace(' ', '-', $string);

       return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

}
