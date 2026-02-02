<?php

declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\ServiceLocator\CheckoutTenantAwareServiceLocator;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;

class CommitOrderProcessorLocator extends CheckoutTenantAwareServiceLocator implements CommitOrderProcessorLocatorInterface
{
    public function getCommitOrderProcessor(string $tenant = null): CommitOrderProcessorInterface
    {
        return $this->locate($tenant);
    }

    public function hasCommitOrderProcessor(string $tenant): bool
    {
        return $this->locator->has($tenant);
    }

    protected function buildNotFoundException(string $tenant): UnsupportedException
    {
        return new UnsupportedException(sprintf(
            'Commit order processor for checkout manager tenant "%s" is not defined. Please check the configuration.',
            $tenant
        ));
    }
}
