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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData;

class IndexFieldSelection
{
    /**
     * @param string|string[]|int $preSelect
     */
    public function __construct(
        public ?string $tenant,
        public string $field,
        public string|array|int|null $preSelect
    ) {
    }

    public function setField(string $field): void
    {
        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string|string[] $preSelect
     */
    public function setPreSelect(array|string|int $preSelect): void
    {
        $this->preSelect = $preSelect;
    }

    /**
     * @return string|string[]|null
     */
    public function getPreSelect(): array|string|int|null
    {
        return $this->preSelect;
    }

    public function setTenant(string $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function getTenant(): ?string
    {
        return $this->tenant;
    }
}
