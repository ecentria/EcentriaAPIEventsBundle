Setting up the bundle
=====================

A: Install Ecentria API Events Bundle
------------------------------------------

Add the repository to your composer.json file 

    "repositories": [
        ...
        { "type": "vcs", "url": "https://github.com/ecentria/EcentriaAPIEventsBundle" }
    ]

Add via composer command

    $ php composer.phar require ecentria/ecentria-APIEvents-bundle dev-master

Or Add via composer.json directly

    "ecentria/ecentria-APIEvents-bundle": "dev-master"

B: Enable the bundle
--------------------

Enable the bundle:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
         new Ecentria\Libraries\EcentriaRestBundle\EcentriaAPIEventsBundle(),
    );
}
```
C: Install Ecentria API Events Bundle
-------------------------------------

Specify in config.yml serializer service and rabbit mq producers to use when it is necessary to resend message to the end of the queue or to send it to quarantine
```
    ecentria_api_events:
        domain_message_serializer: jms_serializer
        resend_producer: old_sound_rabbit_mq.resend_producer
        quarantine_producer: old_sound_rabbit_mq.quarantine_producer
```
