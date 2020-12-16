<?php declare(strict_types=1);

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Tests\Consumer;

use Ecentria\Libraries\EcentriaAPIEventsBundle\Consumer\MessageConsumer;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Exception\ResponseException;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Services\MessageDispatcher;
use JMS\Serializer\SerializerInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class MessageConsumerTest extends TestCase
{
    public function testExecuteShouldFailIfNoMessageClassIsConfigured()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You have to specify Domain class name');

        $messageDispatcher = $this->createMock(MessageDispatcher::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $message = $this->createMock(AMQPMessage::class);

        $target = new MessageConsumer($messageDispatcher, $serializer);
        $target->execute($message);
    }

    public function testExecuteWillCloseChannelOnResponseExceptionWhenStopConsumingAndChannelIsAvailable()
    {
        $messageDispatcher = $this->createMock(MessageDispatcher::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $AMQPMessage = $this->createMock(AMQPMessage::class);
        $AMQPChannel = $this->createMock(AMQPChannel::class);
        $responseException = $this->createMock(ResponseException::class);

        $target = new MessageConsumer($messageDispatcher, $serializer);
        $target->setMessageClassName(\stdClass::class);

        $responseException->method('stopConsuming')->willReturn(true);
        $serializer->method('deserialize')->willThrowException($responseException);

        $AMQPMessage->method('getChannel')->willReturn($AMQPChannel);

        $consumerTag = 'foo';
        $AMQPMessage->method('getConsumerTag')->willReturn($consumerTag);
        $AMQPChannel->method('basic_cancel')->with($consumerTag);

        $responseException->method('getFlag')->willReturn(1);

        $result = $target->execute($AMQPMessage);

        static::assertEquals(1, $result);
    }

    public function testExecuteHandleResponseExceptionWhenStopConsumingAndChannelIsNotAvailable()
    {
        $messageDispatcher = $this->createMock(MessageDispatcher::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $AMQPMessage = $this->createMock(AMQPMessage::class);
        $responseException = $this->createMock(ResponseException::class);

        $target = new MessageConsumer($messageDispatcher, $serializer);
        $target->setMessageClassName(\stdClass::class);

        $responseException->method('stopConsuming')->willReturn(true);
        $serializer->method('deserialize')->willThrowException($responseException);

        $AMQPMessage->method('getChannel')->willReturn(null);

        $consumerTag = 'foo';
        $AMQPMessage->method('getConsumerTag')->willReturn($consumerTag);

        $responseException->method('getFlag')->willReturn(1);

        $result = $target->execute($AMQPMessage);

        static::assertEquals(1, $result);
    }

    public function testExecuteWillReturnTrue()
    {
        $messageDispatcher = $this->createMock(MessageDispatcher::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $AMQPMessage = $this->createMock(AMQPMessage::class);
        $messageBody = '{"value": "bar"}';
        $AMQPMessage->body = $messageBody;

        $target = new MessageConsumer($messageDispatcher, $serializer);
        $messageClassName = Message::class;
        $target->setMessageClassName($messageClassName);

        $message = new Message();
        $message->setValue('bar');

        $serializer->method('deserialize')->with($messageBody, $messageClassName, 'json')->willReturn($message);
        $messageDispatcher->method('dispatchMessage')->with($message);

        $result = $target->execute($AMQPMessage);

        static::assertEquals(true, $result);
    }
}
