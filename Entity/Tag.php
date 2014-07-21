<?php

namespace N1c0\QuoteBundle\Entity;

use N1c0\QuoteBundle\Model\Tag as AbstractTag;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_quote_tag",
 *         parameters = { "id" = "expr(object.getQuote().getId())", "tagId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Tag extends AbstractTag
{
}
