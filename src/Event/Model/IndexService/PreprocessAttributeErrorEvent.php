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

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\Definition\Attribute;
use Throwable;

class PreprocessAttributeErrorEvent extends PreprocessErrorEvent
{
    protected Attribute $attribute;

    protected bool $skipAttribute = false;

    /**
     * PreprocessAttributeErrorEvent constructor.
     */
    public function __construct(Attribute $attribute, Throwable $exception, bool $skipAttribute = false, bool $throwException = true)
    {
        parent::__construct($exception, $throwException);
        $this->attribute = $attribute;
        $this->skipAttribute = $skipAttribute;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function doSkipAttribute(): bool
    {
        return $this->skipAttribute;
    }

    public function setSkipAttribute(bool $skipAttribute): PreprocessErrorEvent
    {
        $this->skipAttribute = $skipAttribute;

        return $this;
    }
}
