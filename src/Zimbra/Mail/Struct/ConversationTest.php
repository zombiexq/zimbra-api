<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Mail\Struct;

/**
 * ConversationTest class
 *
 * @package    Zimbra
 * @subpackage Mail
 * @category   Struct
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class ConversationTest extends FilterTest
{
    /**
     * Constructor method for ConversationTest
     * @param int $index
     * @param string $where
     * @param bool $negative
     * @return self
     */
    public function __construct(
        $index, $where = null, $negative = null
    )
    {
        parent::__construct($index, $negative);
        if(null !== $where)
        {
            $this->property('where', trim($where));
        }
    }

    /**
     * Gets or sets where
     *
     * @param  string $where
     * @return string|self
     */
    public function where($where = null)
    {
        if(null === $where)
        {
            return $this->property('where');
        }
        return $this->property('where', trim($where));
    }

    /**
     * Returns the array representation of this class 
     *
     * @param  string $name
     * @return array
     */
    public function toArray($name = 'conversationTest')
    {
        return parent::toArray($name);
    }

    /**
     * Method returning the xml representative this class
     *
     * @param  string $name
     * @return SimpleXML
     */
    public function toXml($name = 'conversationTest')
    {
        return parent::toXml($name);
    }
}
