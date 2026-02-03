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

use OpenDxp\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use OpenDxp\Model\DataObject\QuantityValue\Unit;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuantityValue implements InterpreterInterface
{
    use OptionsResolverTrait;

    public function interpret(mixed $value, ?array $config = null): float|int|string|null
    {
        $config = $this->resolveOptions($config ?? []);

        if (!empty($value)) {
            if ($config['onlyValue']) {
                $unit = $value->getUnit();
                $value = $value->getValue();

                if ($unit instanceof Unit && $unit->getFactor()) {
                    $value *= $unit->getFactor();
                }

                return $value;
            }
            return $value->__toString();
        }

        return null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('onlyValue', false)
            ->setAllowedTypes('onlyValue', 'bool');
    }
}
