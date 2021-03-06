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

use Zimbra\Common\TypedSequence;
use Zimbra\Struct\Base;

/**
 * RetentionPolicyPurge struct class
 *
 * @package    Zimbra
 * @subpackage Mail
 * @category   Struct
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2013 by Nguyen Van Nguyen.
 */
class RetentionPolicyPurge extends Base
{
    /**
     * Purge policies
     * @var TypedSequence<Policy>
     */
    private $_policy;

    /**
     * Constructor method for RetentionPolicyPurge
     * @param array $policy Purge Policies
     * @return self
     */
    public function __construct(array $policy = array())
    {
    	parent::__construct();
        $this->_policy = new TypedSequence('Zimbra\Mail\Struct\Policy', $policy);

        $this->on('before', function(Base $sender)
        {
            if($sender->policy()->count())
            {
                $sender->child('policy', $sender->policy()->all());
            }
        });
    }

    /**
     * Add policy
     *
     * @param  Policy $policy
     * @return self
     */
    public function addPolicy(Policy $policy)
    {
        $this->_policy->add($policy);
        return $this;
    }

    /**
     * Gets policy sequence
     *
     * @return Sequence
     */
    public function policy()
    {
        return $this->_policy;
    }

    /**
     * Returns the array representation of this class 
     *
     * @param  string $name
     * @return array
     */
    public function toArray($name = 'purge')
    {
        return parent::toArray($name);
    }

    /**
     * Method returning the xml representative this class
     *
     * @param  string $name
     * @return SimpleXML
     */
    public function toXml($name = 'purge')
    {
        return parent::toXml($name);
    }
}
