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

use Ecentria\Libraries\EcentriaAPIEventsBundle\Exception\ResponseException;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\MessageInterface;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
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
     * Producer used to resend message to the end of the queue
     *
     * @var Producer
     */
    private $requeueAsNewProducer;

    /**
     * Constructor
     *
     * @param MessageDispatcher   $messageDispatcher  Message dispatcher
     * @param SerializerInterface $serializer         Message serializer
     * @param Producer            $resendProducer     Producer used to resend message to the end of the queue
     */
    public function __construct(
        MessageDispatcher $messageDispatcher,
        SerializerInterface $serializer,
        Producer $resendProducer
    )
    {
        $this->messageDispatcher = $messageDispatcher;
        $this->serializer = $serializer;
        $this->requeueAsNewProducer = $resendProducer;
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
        } catch (ResponseException $e) {
            if ($e->stopConsuming()) {
                $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
            }
            $payload = $e->getPayload();
            if (!$payload) {
                $payload = $message;
            }
            switch ($e->getFlag()) {
                case ResponseException::NACK_REQUEUE:
                    return ConsumerInterface::MSG_SINGLE_NACK_REQUEUE;
                case ResponseException::REJECT_REQUEUE:
                    return ConsumerInterface::MSG_REJECT_REQUEUE;
                case ResponseException::REJECT:
                    return ConsumerInterface::MSG_REJECT;
                case ResponseException::ACK_REQUEUE_AS_NEW:
                    $this->resendMessage($payload);
                    return ConsumerInterface::MSG_REJECT;
                default:
                    return ConsumerInterface::MSG_ACK;
            }
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

    /**
     * Publish message, which effectively means resend message to the end of queue
     *
     * @param Message $payload the message
     *
     * @return void
     */
    private function resendMessage(Message $payload)
    {
        $this->requeueAsNewProducer->publish($this->serializer->serialize($payload, 'json'), $payload->getSource());
    }
}
