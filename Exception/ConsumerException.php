<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2016, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

/**
 * Abstract Flow Exception
 *
 * @copyright   2016 OpticsPlanet, Inc
 * @author      Eugene Boiarynov <ievgen.boiarynov@opticsplanet.com>
 */
class ConsumerException extends \Exception
{
    /**
     * Message status flag
     *
     * @var int
     */
    private $flag;

    /**
     * Whether this exception should stop consumer
     *
     * @var bool
     */
    private $stopConsuming;

    public function __construct($flag, $stopConsuming = false)
    {
        if (
        !in_array(
            $flag,
            [
                ConsumerInterface::MSG_ACK,
                ConsumerInterface::MSG_REJECT,
                ConsumerInterface::MSG_REJECT_REQUEUE,
                ConsumerInterface::MSG_SINGLE_NACK_REQUEUE
            ]
        )
        ) {
            throw new \InvalidArgumentException(
                sprintf('RabbitMQ ConsumerException can\'t get %s flag as a parameter', $flag)
            );
        }
        $this->flag = $flag;
        $this->stopConsuming = $stopConsuming;
    }

    public static function ack($stopConsuming = false) {
        return new self(ConsumerInterface::MSG_ACK, $stopConsuming);
    }

    public static function reject($stopConsuming = false) {
        return new self(ConsumerInterface::MSG_REJECT, $stopConsuming);
    }

    public static function rejectAndRequeue($stopConsuming = false) {
        return new self(ConsumerInterface::MSG_REJECT_REQUEUE, $stopConsuming);
    }

    public static function nackAndRequeue($stopConsuming = false) {
        return new self(ConsumerInterface::MSG_SINGLE_NACK_REQUEUE, $stopConsuming);
    }

    /**
     * Returns a flag value
     *
     * @return int
     */
    public function getFlag() {
        return $this->flag;
    }

    public function stopConsuming() {
        return $this->stopConsuming;
    }
}