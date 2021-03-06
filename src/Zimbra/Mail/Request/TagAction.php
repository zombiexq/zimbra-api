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

use Zimbra\Mail\Struct\TagActionSelector;

/**
 * TagAction request class
 * Perform an action on a tag
 *
 * @package    Zimbra
 * @subpackage Mail
 * @category   Request
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class TagAction extends Base
{
    /**
     * Constructor method for TagAction
     * @param  TagActionSelector $action
     * @return self
     */
    public function __construct(TagActionSelector $action)
    {
        parent::__construct();
        $this->child('action', $action);
    }

    /**
     * Get or set action
     *
     * @param  TagActionSelector $action
     * @return TagActionSelector|self
     */
    public function action(TagActionSelector $action = null)
    {
        if(null === $action)
        {
            return $this->child('action');
        }
        return $this->child('action', $action);
    }
}
