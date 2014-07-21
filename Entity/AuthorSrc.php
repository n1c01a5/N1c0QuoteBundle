<?php

namespace N1c0\QuoteBundle\Entity;

use N1c0\QuoteBundle\Model\AuthorSrc as AbstractAuthorSrc;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_quote_authorsrc",
 *         parameters = { "id" = "expr(object.getQuote().getId())", "authorsrcId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class AuthorSrc extends AbstractAuthorSrc
{
}
