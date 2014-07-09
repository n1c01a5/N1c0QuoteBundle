<?php

namespace N1c0\QuoteBundle\Entity;

use N1c0\QuoteBundle\Model\Quote as AbstractQuote;

use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Serializer\XmlRoot("quote")
 *
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_quote",
 *         parameters = { "id" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Quote extends AbstractQuote
{
}
