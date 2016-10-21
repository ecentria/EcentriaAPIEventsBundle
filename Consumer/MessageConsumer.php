<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Consumer;

use Ecentria\Libraries\EcentriaAPIEventsBundle\Exception\LifecircleException;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\MessageInterface;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use  Ecentria\Libraries\EcentriaAPIEventsBundle\Services\MessageDispatcher;

/**
 * Message Consumer
 * Responsibility: Convert amqp message to symfony Message Event, and send it to the MessageManager to dispatch to
 * subscribed callbacks
 *
 * @copyright   2015 OpticsPlanet, Inc
 * @author      Justin Shanks <justin.shanks@opticsplanet.com>, Eugene Boiarynov <ievgen.boiarynov@opticsplanet.com>
 */
class MessageConsumer implements ConsumerInterface
{
    /**
     * Domain message class name
     * Is used for json deserialization
     *
     * @var string
     */
    private $messageClassName;

    /**
     * Message Manager
     *
     * @var MessageDispatcher
     */
    private $messageDispatcher;

    /**
     * Serializer
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Constructor
     *
     * @param MessageDispatcher   $messageDispatcher
     * @param SerializerInterface $serializer
     */
    public function __construct(MessageDispatcher $messageDispatcher, SerializerInterface $serializer)
    {
        $this->messageDispatcher = $messageDispatcher;
        $this->serializer = $serializer;
    }

    /**
     * Execute
     * Returns: 0 - reject and requeue, 1 - remove from the queue
     * 2 - nack and requeue, -1 - reject and drop 
     * 
     *
     * @param AMQPMessage $msg The message
     * @throws \InvalidArgumentException
     *
     * @return mixed Message status
     */
    public function execute(AMQPMessage $msg)
    {
        if (is_null($this->messageClassName)) {
            throw new \InvalidArgumentException('You have to specify Domain class name');
        }
        /** @var MessageInterface $message */
        try {
            $message = $this->serializer
                ->deserialize($msg->body, $this->messageClassName, 'json');
            $this->messageDispatcher->dispatchMessage($message);
        } catch (LifecircleException $e) {
            return $e->getFlag();
        }
        
        return true;
    }

    /**
     * Set domain message class name
     *
     * @param string $messageClassName Class name
     *
     * @return void
     */
    public function setMessageClassName($messageClassName)
    {
        $this->messageClassName = $messageClassName;
    }
}
