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

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker\WorkerInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdList implements InterpreterInterface
{
    use OptionsResolverTrait;

    public function interpret(mixed $value, ?array $config = null): ?string
    {
        $config = $this->resolveOptions($config ?? []);

        $ids = [];

        if (is_array($value)) {
            foreach ($value as $val) {
                if ($val && method_exists($val, 'getId')) {
                    $ids[] = $val->getId();
                }
            }
        } elseif ($value && method_exists($value, 'getId')) {
            $ids[] = $value->getId();
        }

        $delimiter = ',';

        if ($config['multiSelectEncoded']) {
            $delimiter = WorkerInterface::MULTISELECT_DELIMITER;
        }

        $ids = implode($delimiter, $ids);

        return $ids ? $delimiter . $ids . $delimiter : null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('multiSelectEncoded', false)
            ->setAllowedTypes('multiSelectEncoded', 'bool');
    }
}
