<?php

declare(strict_types=1);

namespace App\Adapter\Event\Exception;

use RuntimeException;

/**
 * Thrown when a transport serializer does not support the given envelop.
 */
class UnsupportedEnvelope extends RuntimeException
{
}
