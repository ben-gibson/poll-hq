<?php

declare(strict_types=1);

namespace App\Adapter\Event\Bundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Register Event subscribes.
 */
class EventBundle extends Bundle
{
    public function build(ContainerBuilder $container) : void
    {
        // This must run before Symfony\Component\Messenger\DependencyInjection\MessengerPass
        $container->addCompilerPass(new RegisterSubscribers(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
    }
}
