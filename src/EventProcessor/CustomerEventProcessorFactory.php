<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\RobinHqLib\EventProcessor;


use Emico\RobinHqLib\Client\RobinClient;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class CustomerEventProcessorFactory
{
    /**
     * @param ContainerInterface $container
     * @return CustomerEventProcessor
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CustomerEventProcessor
    {
        return new CustomerEventProcessor($container->get(RobinClient::class));
    }
}
