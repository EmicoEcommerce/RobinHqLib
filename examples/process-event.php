<?php
/**
 * @author Bram Gerritsen <bgerritsen@emico.nl>
 * @copyright (c) Emico B.V. 2017
 */

use Emico\RobinHqLib\Event\CustomerEvent;
use Emico\RobinHqLib\Model\Customer;
use Emico\RobinHqLib\Queue\FileQueue;
use Emico\RobinHqLib\Queue\QueueInterface;
use Emico\RobinHqLib\Queue\Serializer\EventSerializer;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
} else {
    throw new RuntimeException('Error: vendor/autoload.php could not be found. Did you run php composer.phar install?');
}

$container = require __DIR__ . '/container.php';

/** @var QueueInterface $queue */
$queue = $container->get(FileQueue::class);
/** @var EventSerializer $eventSerializer */
$eventSerializer = new EventSerializer();

$customer = new Customer('piet@foo.bar');
$event = new CustomerEvent($customer);

$queue->pushEvent($eventSerializer->serializeEvent($event));