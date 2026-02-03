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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Traits;

use InvalidArgumentException;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait OptionsResolverTrait
{
    /**
     * @var OptionsResolver[]
     */
    protected array $optionsResolvers = [];

    /**
     * Runs options through options resolver. Supports multiple options resolvers identified
     * by name (e.g. for sub-options)
     */
    protected function resolveOptions(array $options, string $resolverName = 'default'): array
    {
        return $this->getOptionsResolver($resolverName)->resolve($options);
    }

    /**
     * Sets up and returns a named options resolver
     */
    protected function getOptionsResolver(string $resolverName = 'default'): OptionsResolver
    {
        if (!isset($this->optionsResolvers[$resolverName])) {
            $this->optionsResolvers[$resolverName] = new OptionsResolver();
            $this->configureOptionsResolver($resolverName, $this->optionsResolvers[$resolverName]);
        }

        return $this->optionsResolvers[$resolverName];
    }

    /**
     * Set up options resolver (add defaults, set required fields, ...)
     *
     *
     * @throws InvalidArgumentException If no resolver with the given name is supported
     */
    abstract protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver);
}
