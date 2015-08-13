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

/**
 * Message Model interface
 *
 * @author Eugene Boiarynov <ievgen.boiarynov@opticsplanet.com>
 */
interface MessageInterface
{
    /**
     * Message source
     *
     * A period separated path that identifies where the change originated. Example MyAppDb.customerTable.columnName

     * @return string
     */
    public function getSource();
}
