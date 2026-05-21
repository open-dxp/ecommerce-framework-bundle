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
use Symfony\Component\OptionsResolver\OptionsResolver;

class StructuredTable implements InterpreterInterface
{
    use OptionsResolverTrait;

    public function interpret(mixed $value, ?array $config = null): ?string
    {
        $config = $this->resolveOptions($config ?? []);

        $getter = 'get' . ucfirst((string) $config['tablerow']) . '__' . ucfirst((string) $config['tablecolumn']);
        if (!$value) {
            return null;
        }
        if (!$value instanceof \OpenDxp\Model\DataObject\Data\StructuredTable) {
            return null;
        }
        if (isset($config['defaultUnit'])) {
            return $value->$getter() . ' ' . $config['defaultUnit'];
        }

        return $value->$getter();
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver->setDefined('defaultUnit');

        foreach (['tablerow', 'tablecolumn'] as $field) {
            $resolver
                ->setDefined($field)
                ->setAllowedTypes($field, ['string', 'int']); // TODO does int make sense?
        }
    }
}
