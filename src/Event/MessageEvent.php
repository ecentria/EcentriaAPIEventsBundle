<?php
declare(strict_types=1);
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Event;

use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\MessageInterface;
use Symfony\Component\EventDispatcher\Event;
use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message;

/**
 * Message Model
 *
 * Responsibility: defines a symfony event that contains a message object, used with the MessageManger service
 *
 * @author Justin Shanks <justin.shanks@opticsplanet.com>
 */
class MessageEvent extends Event
{

    /**
     * Message
     *
     * @var Message
     */
    private $message;

    /**
     * Get message
     *
     * @return MessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get message
     *
     * @param MessageInterface $message Message
     */
    public function setMessage(MessageInterface $message)
    {
        $this->message = $message;
    }
}
