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

use Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message;

/**
 * Exception to deliver extensive response from the listener
 *
 * Encapsulates information:
 * - acknowledge status flag if default ACK doesn't work for particular case
 * - whether consumer should stop its execution
 *
 * @copyright   2016 OpticsPlanet, Inc
 * @author      Dmytro Bazavluk <dmitriy.bazavluk@opticsplanet.com>
 */
class ResponseException extends ConsumerException
{

    /**
     * Flag for message ack
     */
    const ACK = 1;

    /**
     * Flag single for message nack and requeue
     */
    const NACK_REQUEUE = 2;

    /**
     * Flag for reject and requeue message to the same spot in main queue
     */
    const REJECT_REQUEUE = 3;

    /**
     * Flag for reject and drop message
     */
    const REJECT = 4;

    /**
     * Flag for ack and resend message to the end of main queue
     */
    const ACK_REQUEUE_AS_NEW = 5;

    /**
     * Response status flag
     * Possible values are listed here:
     * \OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface
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

    /**
     * Payload message to be used for some responses
     *
     * @var Message
     */
    private $payload;

    /**
     * Constructor
     *
     * @param string  $flag          One of the message flag defined
     *                               inside \OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface
     * @param bool    $stopConsuming Whether it is necessary to stop the consumer or not
     * @param Message $message       Message to be used for some responses
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($flag, $stopConsuming = false, Message $payload = null)
    {
        if (
            !in_array(
                $flag,
                [
                    self::ACK,
                    self::NACK_REQUEUE,
                    self::REJECT_REQUEUE,
                    self::REJECT,
                    self::ACK_REQUEUE_AS_NEW
                ]
            )
        ) {
            throw new \InvalidArgumentException(
                sprintf('RabbitMQ ConsumerException can\'t get %s flag as a parameter', $flag)
            );
        }
        $this->flag = $flag;
        $this->stopConsuming = $stopConsuming;
        $this->payload = $payload;
    }

    /**
     * Creates exception with self::ACK flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createAck($stopConsuming = false)
    {
        return new self(self::ACK, $stopConsuming);
    }

    /**
     * Creates exception with self::REJECT flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createReject($stopConsuming = false)
    {
        return new self(self::REJECT, $stopConsuming);
    }

    /**
     * Creates exception with self::REJECT_REQUEUE flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createRejectAndRequeue($stopConsuming = false)
    {
        return new self(self::REJECT_REQUEUE, $stopConsuming);
    }

    /**
     * Creates exception with self::NACK_REQUEUE flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createNackAndRequeue($stopConsuming = false)
    {
        return new self(self::NACK_REQUEUE, $stopConsuming);
    }

    /**
     * Creates exception with self::ACK_REQUEUE_AS_NEW flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createAckAndRequeueAsNew($stopConsuming = false, Message $message = null)
    {
        return new self(self::ACK_REQUEUE_AS_NEW, $stopConsuming, $message);
    }

    /**
     * Returns a flag value
     *
     * @return int
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Returns payload message
     *
     * @return Message
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Returns true if it is necessary to stop the consumer, false otherwise
     *
     * @return bool
     */
    public function stopConsuming()
    {
        return $this->stopConsuming;
    }
}
