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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model\IndexService;

use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

class PreprocessErrorEvent extends Event
{
    /**
     * PreprocessErrorEvent constructor.
     */
    public function __construct(
        protected Throwable $exception,
        protected bool $throwException = true,
        protected int $subObjectId = 0
    ) {
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function setThrowException(bool $throwException): void
    {
        $this->throwException = $throwException;
    }

    public function doThrowException(): bool
    {
        return $this->throwException;
    }

    public function getSubObjectId(): int
    {
        return $this->subObjectId;
    }

    public function setSubObjectId(int $subObjectId): void
    {
        $this->subObjectId = $subObjectId;
    }
}
