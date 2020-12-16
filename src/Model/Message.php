<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaAPIEventsBundle\Model;

use DateTime;
use JMS\Serializer\Annotation\Type;

/**
 * Message Model
 *
 * Responsibility: defines a valid message object, used with the MessageManger service
 *
 * @author Justin Shanks <justin.shanks@opticsplanet.com>
 */
class Message implements MessageInterface
{
    const SOURCE_TYPE_DATA_SOURCE      = 'data-source';
    const SOURCE_TYPE_SERVICE_RESOURCE = 'service-resource';
    const SOURCE_TYPE_SERVICE_WORK     = 'service-work';

    const OPERATION_CREATE = 'create';
    const OPERATION_UPDATE = 'update';
    const OPERATION_DELETE = 'delete';
    const OPERATION_WORK   = 'work';

    /**
     * Message id
     *
     * @Type("string")
     *
     * Primary identifier
     * @var string|int
     */
    private $id;

    /**
     * Message sourceType
     *
     * Identifies the source type of the message.
     * - Data-source means that this message provides details about
     *     a change in the underlying data source of a service.  Date source messages should
     *     only be consumer by the service that is considered the master data manager for that data.
     * - Service-resource means that a related service resource has changed.
     * - Service-work means a task to be completed.
     *
     * Possible Values: data-source|service-resource
     * @var string
     *
     * @Type("string")
     */
    private $sourceType;

    /**
     * Message source
     *
     * A period separated path that identifies where the change originated. Example MyAppDb.customerTable.columnName
     * @var string
     *
     * @Type("string")
     */
    private $source;

    /**
     * Message operation
     *
     * create|update|delete|work
     * @var string
     *
     * @Type("string")
     */
    private $operation;

    /**
     * Message value
     *
     * content of the message (null if delete, new value if update or create). this value is stored as a string and can be anything from an int to serialized data
     * @var string|null|array
     *
     * @Type("array")
     */
    private $value;

    /**
     * Message operation
     *
     * if delete or or update, this property is expected to have the old value
     * this value is stored as a string and can be anything from an int to serialized data
     * @var string|null
     *
     * @Type("array")
     */
    private $previousValue;

    /**
     * Message operation datetime
     *
     * the datetime that the operation happened on the source system
     * @var datetime
     *
     * @Type("DateTime")
     */
    private $operationDatetime;

    /**
     * Message datetime
     *
     * the datetime that the message was generated
     *
     * @var DateTime
     *
     * @Type("DateTime")
     */
    private $messageDatetime;

    public function __construct()
    {
        $this->messageDatetime = new \DateTime();
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return datetime
     */
    public function getMessageDatetime()
    {
        return $this->messageDatetime;
    }

    /**
     * @param \DateTime $messageDatetime
     */
    public function setMessageDatetime(\DateTime $messageDatetime)
    {
        $this->messageDatetime = $messageDatetime;
    }

    /**
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return \DateTime
     */
    public function getOperationDatetime()
    {
        return $this->operationDatetime;
    }

    /**
     * @param \DateTime $operationDatetime
     */
    public function setOperationDatetime(\DateTime $operationDatetime)
    {
        $this->operationDatetime = $operationDatetime;
    }

    /**
     * @return null|string
     */
    public function getPreviousValue()
    {
        return $this->previousValue;
    }

    /**
     * @param null|string $previousValue
     */
    public function setPreviousValue($previousValue)
    {
        $this->previousValue = $previousValue;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @param string $sourceType
     */
    public function setSourceType($sourceType)
    {
        $this->sourceType = $sourceType;
    }

    /**
     * @return null|string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param null|string|array $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

}
