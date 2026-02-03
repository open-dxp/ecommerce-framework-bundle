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

use OpenDxp\Model\DataObject\Data\ObjectMetadata;
use OpenDxp\Model\Element\ElementInterface;
use OpenDxp\Model\Element\Service;

class DefaultRelations implements RelationInterpreterInterface
{
    public function interpret(mixed $value, ?array $config = null): array
    {
        $result = [];

        if ($value instanceof ObjectMetadata) {
            $value = $value->getObject();
        }

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof ObjectMetadata) {
                    $v = $v->getObject();
                }

                $result[] = ['dest' => $v->getId(), 'type' => Service::getElementType($v)];
            }
        } elseif ($value instanceof ElementInterface) {
            $result[] = ['dest' => $value->getId(), 'type' => Service::getElementType($value)];
        }

        return $result;
    }
}
