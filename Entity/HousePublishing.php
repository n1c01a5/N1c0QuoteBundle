<?php

namespace N1c0\QuoteBundle\Entity;

use N1c0\QuoteBundle\Model\HousePublishing as AbstractHousePublishing;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_quote_housepublishing",
 *         parameters = { "id" = "expr(object.getQuote().getId())", "housepublishingId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class HousePublishing extends AbstractHousePublishing
{
}
