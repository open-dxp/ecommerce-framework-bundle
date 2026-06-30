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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\TokenManager;

use Knp\Component\Pager\PaginatorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractVoucherTokenType;

class TokenManagerFactory implements TokenManagerFactoryInterface
{
    /**
     * @var TokenManagerInterface[]
     */
    private array $tokenManagers = [];

    public function __construct(
        private array $mapping,
        protected PaginatorInterface $paginator
    ) {
    }

    public function getTokenManager(AbstractVoucherTokenType $configuration): TokenManagerInterface
    {
        $id = $configuration->getObject()->getId();
        $type = $configuration->getType();

        if (isset($this->tokenManagers[$id])) {
            return $this->tokenManagers[$id];
        }

        if (!isset($this->mapping[$type])) {
            throw new InvalidConfigException(sprintf('Token Manager for type %s is not defined.', $type));
        }

        $tokenManagerClass = $this->mapping[$type];
        $this->tokenManagers[$id] = new $tokenManagerClass($configuration, $this->paginator);

        return $this->tokenManagers[$id];
    }
}
