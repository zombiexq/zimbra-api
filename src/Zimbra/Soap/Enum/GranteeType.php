<?php
/**
 * This file is part of the Zimbra API in PHP library.
 *
 * © Nguyen Van Nguyen <nguyennv1981@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zimbra\Soap\Enum;

/**
 * GranteeType class
 * @package   Zimbra
 * @category  Soap
 * @author    Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright Copyright © 2013 by Nguyen Van Nguyen.
 */
class GranteeType
{
    /**
     * Constant for value 'usr'
     * @return string 'usr'
     */
    const USR = 'usr';

    /**
     * Constant for value 'grp'
     * @return string 'grp'
     */
    const GRP = 'grp';

    /**
     * Constant for value 'egp'
     * @return string 'egp'
     */
    const EGP = 'egp';

    /**
     * Constant for value 'all'
     * @return string 'all'
     */
    const ALL = 'all';

    /**
     * Constant for value 'dom'
     * @return string 'dom'
     */
    const DOM = 'dom';

    /**
     * Constant for value 'gst'
     * @return string 'gst'
     */
    const GST = 'gst';

    /**
     * Constant for value 'key'
     * @return string 'key'
     */
    const KEY = 'key';

    /**
     * Constant for value 'pub'
     * @return string 'pub'
     */
    const PUB = 'pub';

    /**
     * Constant for value 'email'
     * @return string 'email'
     */
    const EMAIL = 'email';

    /**
     * Return true if value is allowed
     * @param  string $type
     * @return bool true|false
     */
    public static function isValid($type)
    {
        $validValues = array(
            self::USR,
            self::GRP,
            self::EGP,
            self::ALL,
            self::DOM,
            self::GST,
            self::KEY,
            self::PUB,
            self::EMAIL,
        );
        return in_array($type, $validValues);
    }
}