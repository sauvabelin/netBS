<?php

namespace Ovesco\WhatsappBundle;

use Ovesco\WhatsappBundle\DependencyInjection\Compiler\ScenarioPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OvescoWhatsappBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ScenarioPass());
    }
}
