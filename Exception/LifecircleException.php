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

/**
 * Abstract Flow Exception
 * 
 * @copyright   2016 OpticsPlanet, Inc
 * @author      Eugene Boiarynov <ievgen.boiarynov@opticsplanet.com>
 */
abstract class LifecircleException extends \Exception
{
    /**
     * Returns a flag value
     * 
     * @return int
     */
    abstract public function getFlag();
}