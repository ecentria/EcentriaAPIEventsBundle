<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Services;

use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\MessageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Event\MessageEvent;

/**
 * Message Manager Service
 *
 * Responsibility: Manages creation, sending, and receiving interfaces, and aggregation of domain messages.
 * - triggers symfony event from message
 * - produces list of all routing keys this api should listen on, meant to publish to bindings using adapter
 *
 * @author Justin Shanks <justin.shanks@opticsplanet.com>
 */
class MessageDispatcher
{
    /**
     * Domain event prefix
     *
     * @var string
     */
    private $eventPrefix;

    /**
     * Symfony Event Dispatcher
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     *
     */
    private $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     *
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Sets domain event prefix
     *
     * @param string $eventPrefix Domain event prefix
     *
     */
    public function setEventPrefix($eventPrefix)
    {
        $this->eventPrefix = $eventPrefix;
    }

    /**
     * Converts a message to a MessageEvent and dispatches it
     *
     * @param MessageInterface $message the input data
     *
     */
    public function dispatchMessage(MessageInterface $message)
    {
        $event = $this->getMessageEventObject();
        $event->setMessage($message);
        $this->dispatcher->dispatch($this->getEventPrefix() . $message->getSource(), $event);
    }

    /**
     * returns the message object to use, overload to alter this. @todo setup interface for this
     *
     * @return \Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message
     *
     */
    public function getMessageObject()
    {
        return new Message();
    }

    /**
     * returns the message event object to use, overload to alter this. @todo setup interface for this
     *
     * @return  \Ecentria\Libraries\EcentriaAPIEventsBundle\Event\MessageEvent
     *
     */
    public function getMessageEventObject()
    {
        return new MessageEvent();
    }

    /**
     * returns all the listeners in the app that are listening to domain messages
     *
     * @return array returns array of listener details (name and callback reference) by event key
     *
     */
    public function getInternalEventListeners() {
        $all_listeners = $this->dispatcher->getListeners();


        $domain_listeners = array();
        $prefix = $this->getEventPrefix();
        $prefix_length = strlen($prefix);
        foreach ($all_listeners as $event_name => $listener_by_event) {

            // if this event key is a domain message
            if (substr($event_name,0,$prefix_length) === $prefix) {
                $domain_listeners[$event_name] = $listener_by_event;
            }

        }
        return $domain_listeners;
    }

    /**
     * returns the list of domain events keys that this app is listening too
     *
     * @return array returns array of event keys that start with the configured prefix
     *
     */
    public function getListenerDomainKeys()
    {
        $domain_listeners = $this->getInternalEventListeners();
        $keys = array();

        $prefix = $this->getEventPrefix();
        $prefix_length = strlen($prefix);

        foreach ($domain_listeners as $key => $listener)
        {
            $keys[] = substr($key,($prefix_length));
        }
        return $keys;
    }

    /**
     * returns the injected dispatcher
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     *
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * returns the configured prefix for domain messages
     *
     * @return string
     *
     */
    public function getEventPrefix()
    {
        return $this->eventPrefix;
    }

}