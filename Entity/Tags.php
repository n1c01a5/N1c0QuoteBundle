<?php

namespace N1c0\DissertationBundle\Entity;

use N1c0\DissertationBundle\Model\Tags as AbstractTags;

use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *     name = "self",
 *     href = @Hateoas\Route(
 *         "api_1_get_dissertation_tags",
 *         parameters = { "id" = "expr(object.getDissertation().getId())", "tagsId" = "expr(object.getId())" },
 *         absolute = true
 *     )
 * )
 */
abstract class Tags extends AbstractTags
{

}
