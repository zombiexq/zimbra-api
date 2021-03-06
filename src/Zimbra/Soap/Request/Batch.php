<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Soap\Request;

use Zimbra\Soap\Request;
use Zimbra\Common\SimpleXML;
use Zimbra\Common\TypedSequence;

/**
 * Batch request class in Zimbra API PHP, not to be instantiated.
 * 
 * @package   Zimbra
 * @category  API
 * @author    Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright Copyright © 2014 by Nguyen Van Nguyen.
 */
class Batch extends Request
{
    /**
     * Attributes specified as key value pairs
     * @var Sequence
     */
    private $_requests;

    /**
     * Batch request constructor
     * @param  array $requests
     * @return self
     */
    public function __construct(array $requests = array())
    {
        parent::__construct();
        $this->_requests = new TypedSequence('Zimbra\Soap\Request', $requests);
    }

    /**
     * Gets or sets onerror
     *
     * @param  string $onerror
     * @return string|self
     */
    public function onerror($onerror = null)
    {
        if(null === $onerror)
        {
            return $this->property('onerror');
        }
        return $this->property('onerror', trim($onerror));
    }

    /**
     * Add a request
     *
     * @param  Request $request
     * @return self
     */
    public function addRequest(Request $request)
    {
        $this->_requests->add($request);
        return $this;
    }

    /**
     * Gets request sequence
     *
     * @return Sequence
     */
    public function requests()
    {
        return $this->_requests;
    }

    /**
     * Returns the array representation of this class 
     *
     * @param  string $name
     * @return array
     */
    public function toArray($name = null)
    {
        $name = empty($name) ? $this->requestName() : $name;
        $arr = array(
            '_jsns' => $this->xmlNamespace(),
            'onerror' => $this->onerror(),
        );
        foreach ($this->_requests as $key => $request)
        {
            $reqArr = $request->toArray();
            $arr[$request->requestName()] = $reqArr[$request->requestName()];
        }
        return array($this->requestName() => $arr);
    }

    /**
     * Method returning the xml representation of this class
     *
     * @param  string $name
     * @return SimpleXML
     */
    public function toXml($name = null)
    {
        $name = empty($name) ? $this->requestName() : $name;
        $xml = new SimpleXML('<'.$name.' />');
        foreach ($this->_requests as $key => $request)
        {
            $requestXml = $request->toXml();
            $requestXml->addAttribute('requestId', $key);
            $xml->append($requestXml, $request->xmlNamespace());
        }
        return $xml;
    }
}