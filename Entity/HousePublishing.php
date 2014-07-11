<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\HousePublishing as AbstractHousePublishing;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation_housePublishing",
 *         parameters = { "id" = "expr(object.getDissertation().getId())", "housePublishingId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class HousePublishing extends AbstractHousePublishing
{

}
