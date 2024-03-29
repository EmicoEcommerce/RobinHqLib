<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\RobinHqLib\Di;

use Emico\RobinHqLib\Client\RobinClient;
use Emico\RobinHqLib\Client\RobinClientFactory;
use Emico\RobinHqLib\Config\Config;
use Emico\RobinHqLib\Di\Exception\InvalidFactoryException;
use Emico\RobinHqLib\Di\Exception\ServiceNotFoundException;
use Emico\RobinHqLib\EventProcessor\CustomerEventProcessor;
use Emico\RobinHqLib\EventProcessor\CustomerEventProcessorFactory;
use Emico\RobinHqLib\EventProcessor\OrderEventProcessor;
use Emico\RobinHqLib\EventProcessor\OrderEventProcessorFactory;
use Emico\RobinHqLib\Logger\LoggerFactory;
use Emico\RobinHqLib\Queue\FileQueue;
use Emico\RobinHqLib\Queue\FileQueueFactory;
use Emico\RobinHqLib\Queue\QueueInterface;
use Emico\RobinHqLib\Server\RestApiServer;
use Emico\RobinHqLib\Server\RestApiServerFactory;
use Emico\RobinHqLib\Service\CustomerService;
use Emico\RobinHqLib\Service\CustomerServiceFactory;
use Emico\RobinHqLib\Service\EventProcessingService;
use Emico\RobinHqLib\Service\EventProcessingServiceFactory;
use Emico\RobinHqLib\Service\OrderService;
use Emico\RobinHqLib\Service\OrderServiceFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private array $factories = [
        FileQueue::class => FileQueueFactory::class,
        QueueInterface::class => FileQueueFactory::class,
        LoggerInterface::class => LoggerFactory::class,
        RobinClient::class => RobinClientFactory::class,
        EventProcessingService::class => EventProcessingServiceFactory::class,
        CustomerEventProcessor::class => CustomerEventProcessorFactory::class,
        OrderEventProcessor::class => OrderEventProcessorFactory::class,
        CustomerService::class => CustomerServiceFactory::class,
        OrderService::class => OrderServiceFactory::class,
        RestApiServer::class => RestApiServerFactory::class,
    ];

    /**
     * @var array
     */
    private array $instances = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->instances[Config::class] = $config;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws InvalidFactoryException
     * @throws ServiceNotFoundException
     */
    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!$this->has($id)) {
            throw new ServiceNotFoundException(sprintf('Service with id "%s" can not be found', $id));
        }

        if (\is_string($this->factories[$id])) {
            $this->factories[$id] = new $this->factories[$id]();
        }

        if (!\is_callable($this->factories[$id])) {
            throw new InvalidFactoryException('No factory configured of factory is not callable for id ' . $id);
        }

        $this->instances[$id] = $this->factories[$id]($this);
        return $this->instances[$id];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }

    /**
     * @param string $id
     * @param callable $factory
     * @return $this
     */
    public function setFactory(string $id, callable $factory): self
    {
        $this->factories[$id] = $factory;
        return $this;
    }

    /**
     * @param string $id
     * @param mixed $instance
     * @return $this
     */
    public function setInstance(string $id, mixed $instance): self
    {
        $this->instances[$id] = $instance;
        return $this;
    }
}