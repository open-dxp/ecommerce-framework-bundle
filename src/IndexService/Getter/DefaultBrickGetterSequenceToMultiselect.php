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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Getter;

use InvalidArgumentException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultBrickGetterSequenceToMultiselect implements GetterInterface
{
    use OptionsResolverTrait;

    public function get(object $object, ?array $config = null): mixed
    {
        $config = $this->resolveOptions($config ?? []);
        $sourceList = $config['source'];

        // normalize single entry to list
        if (isset($sourceList['brickfield'])) {
            $sourceList = [$sourceList];
        }

        $values = [];
        foreach ($sourceList as $source) {
            $source = $this->resolveOptions((array)$source, 'source');

            $brickContainerGetter = 'get' . ucfirst((string) $source['brickfield']);

            if (method_exists($object, $brickContainerGetter)) {
                $brickContainer = $object->$brickContainerGetter();

                $brickGetter = 'get' . ucfirst((string) $source['bricktype']);
                $brick = $brickContainer->$brickGetter();
                if ($brick) {
                    $fieldGetter = 'get' . ucfirst((string) $source['fieldname']);
                    $value = $brick->$fieldGetter();

                    if ($source['invert']) {
                        $value = !$value;
                    }

                    if ($value) {
                        if (is_bool($value) || $source['forceBool']) {
                            $values[] = $source['fieldname'];
                        } elseif (is_array($value)) {
                            $values = [...$values, ...$value];
                        } else {
                            $values[] = $value;
                        }
                    }
                }
            } else {
                $fieldGetter = 'get' . ucfirst((string) $source['fieldname']);
                if (method_exists($object, $fieldGetter)) {
                    $value = $object->$fieldGetter();

                    if ($source['invert']) {
                        $value = !$value;
                    }

                    if ($value) {
                        if (is_bool($value) || $source['forceBool']) {
                            $values[] = $source['fieldname'];
                        } elseif (is_array($value)) {
                            $values = [...$values, ...$value];
                        } else {
                            $values[] = $value;
                        }
                    }
                }
            }
        }

        return $values;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        if ('default' === $resolverName) {
            $resolver
                ->setDefined('source')
                ->setAllowedTypes('source', 'array');
        } elseif ('source' === $resolverName) {
            // brickfield, bricktype, fieldname
            DefaultBrickGetter::setupBrickGetterOptionsResolver($resolver);

            $resolver->setDefaults([
                'invert' => false,
                'forceBool' => false,
            ]);

            foreach (['invert', 'forceBool'] as $boolType) {
                $resolver->setAllowedTypes($boolType, 'bool');
            }
        } else {
            throw new InvalidArgumentException(sprintf('Resolver with name "%s" is not defined', $resolverName));
        }
    }
}
