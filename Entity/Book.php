<?php

namespace N1c0\QuoteBundle\Entity;

use N1c0\QuoteBundle\Model\Book as AbstractBook;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_quote_book",
 *         parameters = { "id" = "expr(object.getQuote().getId())", "bookId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Book extends AbstractBook
{

}
