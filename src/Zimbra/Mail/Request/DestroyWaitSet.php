<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Mail\Request;

/**
 * DestroyWaitSet request class
 * Use this to close out the waitset.
 * Note that the server will automatically time out a wait set if there is no reference to it for (default of) 20 minutes.
 * WaitSet: scalable mechanism for listening for changes to one or more accounts
 *
 * @package    Zimbra
 * @subpackage Mail
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class DestroyWaitSet extends Base
{
    /**
     * Constructor method for DestroyWaitSet
     * @param  string $waitSet
     * @return self
     */
    public function __construct($waitSet)
    {
        parent::__construct();
        $this->property('waitSet', trim($waitSet));
    }

    /**
     * Get or set waitSet
     *
     * @param  string $waitSet
     * @return string|self
     */
    public function waitSet($waitSet = null)
    {
        if(null === $waitSet)
        {
            return $this->property('waitSet');
        }
        return $this->property('waitSet', trim($waitSet));
    }
}
