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
use OpenDxp\Model\DataObject\Data\StructuredTable;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultStructuredTable implements InterpreterInterface
{
    use OptionsResolverTrait;

    public function interpret(mixed $value, ?array $config = null): mixed
    {
        $config = $this->resolveOptions($config ?? []);

        if ($value instanceof StructuredTable) {
            $data = $value->getData();

            return $data[$config['row']][$config['column']];
        }

        return null;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        foreach (['column', 'row'] as $field) {
            $resolver
                ->setDefined($field)
                ->setAllowedTypes($field, ['string', 'int']);
        }
    }
}
