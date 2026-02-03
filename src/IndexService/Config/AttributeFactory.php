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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config;

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\Definition\Attribute;
use Psr\Container\ContainerInterface;

/**
 * Builds attributes from config. Getters and interpreters are scoped service locators
 * containing only the configured getters/interpreters.
 */
class AttributeFactory
{
    private ContainerInterface $getters;

    private ContainerInterface $interpreters;

    public function __construct(
        ContainerInterface $getters,
        ContainerInterface $interpreters
    ) {
        $this->getters = $getters;
        $this->interpreters = $interpreters;
    }

    public function createAttribute(array $config): Attribute
    {
        $getter = null;
        if (null !== $getterId = $config['getter_id'] ?? null) {
            $getter = $this->getters->get($getterId);
        }

        $interpreter = null;
        if (null !== $interpreterId = $config['interpreter_id'] ?? null) {
            $interpreter = $this->interpreters->get($interpreterId);
        }

        return new Attribute(
            $config['name'],
            $config['field_name'] ?? null,
            $config['type'] ?? null,
            $config['locale'] ?? null,
            $config['filter_group'] ?? null,
            $config['options'] ?? [],
            $getter,
            $config['getter_options'] ?? [],
            $interpreter,
            $config['interpreter_options'] ?? [],
            $config['hide_in_fieldlist_datatype'] ?? false
        );
    }
}
