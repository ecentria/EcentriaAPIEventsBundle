<?php
declare(strict_types=1);
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2020, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Tests\Integration;

use Ecentria\Libraries\EcentriaAPIEventsBundle\Consumer\MessageConsumer;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Event\MessageEvent;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Services\MessageDispatcher;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Tests\Integration\App\AppKernel;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class IntegrationMessageConsumerTest extends KernelTestCase
{
    /**
     * @return mixed
     */
    public function getContainer()
    {
        return self::$kernel->getContainer();
    }

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        static::bootKernel(['debug' => true]);
    }

    /**
     * Get kernel class
     *
     * @return string
     */
    protected static function getKernelClass()
    {
        return AppKernel::class;
    }

    public function testExecuteWhenEventIsRegistered()
    {
        /** @var MessageDispatcher $messageDispatcher */
        $messageDispatcher = self::getContainer()->get('ecentria.api.domain_message.manager');
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::getContainer()->get('event_dispatcher');
        $serializer = self::getContainer()->get('jms_serializer.serializer');

        $messageBody = '{"value": ["bar"], "source": "foo"}';
        $AMQPMessage = new AMQPMessage($messageBody);

        $target = new MessageConsumer($messageDispatcher, $serializer);
        $messageClassName = Message::class;
        $target->setMessageClassName($messageClassName);

        $eventDispatcher->addListener(
            'domain.foo',
            function (MessageEvent $event) {
                $this->assertInstanceOf(Message::class, $event->getMessage());
                $this->assertEquals('foo', $event->getMessage()->getSource());
                $this->assertEquals(['bar'], $event->getMessage()->getValue());
            }
        );

        $result = $target->execute($AMQPMessage);

        static::assertEquals(true, $result);
    }

    public function testExecuteWhenEventIsNotRegistered()
    {
        /** @var MessageDispatcher $messageDispatcher */
        $messageDispatcher = self::getContainer()->get('ecentria.api.domain_message.manager');
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::getContainer()->get('event_dispatcher');
        $serializer = self::getContainer()->get('jms_serializer.serializer');

        $messageBody = '{"value": ["bar"], "source": "foo"}';
        $AMQPMessage = new AMQPMessage($messageBody);

        $target = new MessageConsumer($messageDispatcher, $serializer);
        $messageClassName = Message::class;
        $target->setMessageClassName($messageClassName);

        $eventDispatcher->addListener(
            MessageDispatcher::UNMATCHED_EVENT,
            function (MessageEvent $event) {
                $this->assertInstanceOf(Message::class, $event->getMessage());
                $this->assertEquals('foo', $event->getMessage()->getSource());
                $this->assertEquals(['bar'], $event->getMessage()->getValue());
            }
        );

        $result = $target->execute($AMQPMessage);

        static::assertEquals(true, $result);
    }
}
