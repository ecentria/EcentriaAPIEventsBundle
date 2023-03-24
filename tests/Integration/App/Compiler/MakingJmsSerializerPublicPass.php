<?php declare(strict_types=1);

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Tests\Integration\App\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MakingJmsSerializerPublicPass
 *
 * @author Oleg Andreyev <oleg.andreyev@ecentria.com>
 */
class MakingJmsSerializerPublicPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->makePublic($container, 'jms_serializer.serializer');
        $this->makePublic($container, 'serializer');
    }

    public function makePublic(ContainerBuilder $container, string $id)
    {
        if ($container->hasDefinition($id)) {
            $definition = $container->getDefinition($id);
            $definition->setPublic(true);
        }

        if ($container->hasAlias($id)) {
            $alias = $container->getAlias($id);
            $alias->setPublic(true);
        }
    }
}
