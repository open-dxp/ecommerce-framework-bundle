<?php
declare(strict_types=1);

/**
 * OpenDXP
 *
 * This source file is licensed under the GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (https://pimcore.com)
 * @copyright  Modification Copyright (c) OpenDXP (https://www.opendxp.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0.html  GNU General Public License version 3 (GPLv3)
 */

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Interpreter;

class Soundex implements InterpreterInterface
{
    public function interpret(mixed $value, ?array $config = null): int
    {
        if (is_array($value)) {
            sort($value);
            $string = implode(' ', $value);
        } else {
            $string = (string)$value;
        }
        $soundex = soundex($string);

        return (int)(ord(substr($soundex, 0, 1)).substr($soundex, 1));
    }
}
