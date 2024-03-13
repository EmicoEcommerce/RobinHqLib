<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

namespace Emico\RobinHqLib\Service;


use Emico\RobinHqLib\Client\RobinClient;
use Emico\RobinHqLib\Event\EventInterface;
use Emico\RobinHqLib\EventProcessor\EventProcessorInterface;
use Emico\RobinHqLib\Queue\Serializer\EventSerializer;
use Exception;
use Psr\Log\LoggerInterface;

class EventProcessingService
{
    private ?EventSerializer $eventSerializer = null;

    /**
     * @var EventProcessorInterface[]
     */
    private array $eventProcessors = [];

    /**
     * @param LoggerInterface $logger
     * @param array $eventProcessors
     */
    public function __construct(private LoggerInterface $logger, array $eventProcessors = [])
    {
        $this->eventProcessors = $eventProcessors;
    }

    /**
     * @param string $event
     */
    public function processEvent(string $event)
    {
        $event = $this->getEventSerializer()->unserializeEvent($event);

        $this->logger->info('Processing ' . $event->getAction() . ' event ' . $event);

        try {
            $this->getEventProcessor($event)->processEvent($event);
        } catch (Exception $ex) {
            $this->logger->error($ex->getMessage());
        }
    }

    /**
     * @param EventInterface $event
     * @return EventProcessorInterface|mixed
     * @throws Exception
     */
    protected function getEventProcessor(EventInterface $event): EventProcessorInterface
    {
        $action = $event->getAction();
        if (!isset($this->eventProcessors[$action])) {
            throw new Exception('No event processor registered for action ' . $action);
        }
        return $this->eventProcessors[$action];
    }

    /**
     * @param string $action
     * @param EventProcessorInterface $eventProcessor
     */
    public function registerEventProcessor(string $action, EventProcessorInterface $eventProcessor)
    {
        $this->eventProcessors[$action] = $eventProcessor;
    }

    /**
     * @return EventSerializer
     */
    public function getEventSerializer(): EventSerializer
    {
        if (!isset($this->eventSerializer)) {
            $this->eventSerializer = new EventSerializer();
        }
        return $this->eventSerializer;
    }
}