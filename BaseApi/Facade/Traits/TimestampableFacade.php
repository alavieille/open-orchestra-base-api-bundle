<?php

namespace OpenOrchestra\BaseApi\Facade\Traits;

/**
 * Trait TimestampableFacade
 */
trait TimestampableFacade
{
    /**
     * @Serializer\Type("DateTime<'d/m/Y H:i:s'>")
     */
    public $createdAt;

    /**
     * @Serializer\Type("DateTime<'d/m/Y H:i:s'>")
     */
    public $updatedAt;
}
