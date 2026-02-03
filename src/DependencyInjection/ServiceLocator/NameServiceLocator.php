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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use Psr\Container\ContainerInterface as PsrContainerInterface;

abstract class NameServiceLocator
{
    protected string $defaultName = 'default';

    public function __construct(protected PsrContainerInterface $locator)
    {
    }

    protected function locate(?string $name = null): mixed
    {
        $name = $this->resolveName($name);

        if (!$this->locator->has($name)) {
            throw $this->buildNotFoundException($name);
        }

        return $this->locator->get($name);
    }

    protected function resolveName(?string $name = null): string
    {
        if (empty($name)) {
            return $this->defaultName;
        }

        return $name;
    }

    abstract protected function buildNotFoundException(string $name): UnsupportedException;
}
