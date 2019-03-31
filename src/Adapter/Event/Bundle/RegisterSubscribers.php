<?php

declare(strict_types=1);

namespace App\Adapter\Event\Bundle;

use App\Domain\Event\EventSubscriber;
use RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function is_a;
use function is_array;
use function is_numeric;
use function is_string;
use function sprintf;

/**
 * Register Event subscribes.
 *
 * Responsible for finding services that are interested in subscribing to an event or events and creating the
 * necessary configuration to wire them up through the Symfony messenger bus.
 */
class RegisterSubscribers implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        foreach ($container->getDefinitions() as $id => $definition) {

            if (!is_a($definition->getClass(), EventSubscriber::class, true)) {
                continue;
            }

            // Convert the subscriptions into message handler tags.
            foreach ($definition->getClass()::eventSubscriptions() as $event => $subscription) {
                if (is_numeric($event)) {
                    $event        = $subscription;
                    $subscription = [];
                }

                if (is_string($subscription)) {
                    $attributes['method'] = $subscription;
                } elseif (is_array($subscription)) {
                    $attributes = $subscription;
                } else {
                    throw new RuntimeException(sprintf(
                        "Invalid event subscription from subscriber '%s'.",
                        $definition->getClass()
                    ));
                }

                // TODO: Inject event bus id.
                $attributes['bus']     = 'messenger.bus.event';
                $attributes['handles'] = $event;

                $definition->addTag('messenger.message_handler', $attributes);
            }
        }
    }
}
