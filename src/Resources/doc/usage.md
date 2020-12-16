Usage
==================================
Available configuration parameters
----------------------------------
``` yaml
ecentria_api_events:
    # message prefix
    domain_message_prefix: 'domain.' #optional
    # serializer service. Should be an instance of JMS\Serializer\SerializerInterface
    domain_message_serializer: jms_serializer #required
    # domain message class name. Instance of MessageInterface
    domain_message_class_name: 'Ecentria\Libraries\EcentriaAPIEventsBundle\Model\Message' #optional
```