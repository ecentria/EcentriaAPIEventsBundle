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
     * Constructor
     *
     * @param string $flag          One of the message flag defined
     *                              inside \OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface
     * @param bool   $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @throws \InvalidArgumentException
     */
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

    /**
     * Creates exception with ConsumerInterface::MSG_ACK flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createAck($stopConsuming = false)
    {
        return new self(ConsumerInterface::MSG_ACK, $stopConsuming);
    }


    /**
     * Creates exception with ConsumerInterface::MSG_REJECT flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createReject($stopConsuming = false)
    {
        return new self(ConsumerInterface::MSG_REJECT, $stopConsuming);
    }


    /**
     * Creates exception with ConsumerInterface::MSG_REJECT_REQUEUE flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createRejectAndRequeue($stopConsuming = false)
    {
        return new self(ConsumerInterface::MSG_REJECT_REQUEUE, $stopConsuming);
    }


    /**
     * Creates exception with ConsumerInterface::MSG_SINGLE_NACK_REQUEUE flag
     *
     * @param bool $stopConsuming Whether it is necessary to stop the consumer or not
     *
     * @return ResponseException
     */
    public static function createNackAndRequeue($stopConsuming = false)
    {
        return new self(ConsumerInterface::MSG_SINGLE_NACK_REQUEUE, $stopConsuming);
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
     * Returns true if it is necessary to stop the consumer, false otherwise
     *
     * @return bool
     */
    public function stopConsuming()
    {
        return $this->stopConsuming;
    }
}
