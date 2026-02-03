<?php

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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Model;

use OpenDxp;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OpenDxp\Localization\IntlFormatter;

class Currency
{
    const LEFT = 'left';

    const RIGHT = 'right';

    const NO_SYMBOL = 'none';

    const USE_SYMBOL = 'sign';

    const USE_SHORTNAME = 'shortname';

    const USE_NAME = 'longname';

    protected string $currencySymbol;

    protected string $currencyName;

    protected array $patternStore = [
        self::NO_SYMBOL => [
            self::LEFT => '#,##0.00',
            self::RIGHT => '#,##0.00',
        ],
        self::USE_SYMBOL => [
            self::LEFT => '¤ #,##0.00',
            self::RIGHT => '#,##0.00 ¤',
        ],
        self::USE_SHORTNAME => [
            self::LEFT => '¤¤ #,##0.00',
            self::RIGHT => '#,##0.00 ¤¤',
        ],
        self::USE_NAME => [
            self::LEFT => '¤¤¤ #,##0.00',
            self::RIGHT => '#,##0.00 ¤¤¤',
        ],
    ];

    /**
     * Currency constructor.
     */
    public function __construct(protected string $currencyShortName)
    {
    }

    protected function getFormatter(): IntlFormatter
    {
        return OpenDxp::getContainer()->get(IntlFormatter::class);
    }

    public function toCurrency(null|float|int|string|Decimal $value, array|string $pattern = 'default'): string
    {
        if ($value === null) {
            return '';
        }

        if (is_array($pattern)) {
            $symbol = $pattern['display'] ?: self::USE_SYMBOL;
            $position = $pattern['position'] ?: self::RIGHT;

            $pattern = $this->patternStore[$symbol][$position] ?: 'default';
        }

        if ($value instanceof Decimal) {
            $value = $value->asString();
        }

        return $this->getFormatter()->formatCurrency($value, $this->currencyShortName, $pattern);
    }

    public function getShortName(): string
    {
        return $this->currencyShortName;
    }

    public function getSymbol(): string
    {
        if (empty($this->currencySymbol)) {
            $result = $this->getFormatter()->formatCurrency(0, $this->currencyShortName, '¤||');
            $parts = explode('||', $result);
            $this->currencySymbol = $parts[0];
        }

        return $this->currencySymbol;
    }

    public function getName(): string
    {
        if (empty($this->currencyName)) {
            $result = $this->getFormatter()->formatCurrency(0, $this->currencyShortName, '¤¤¤||');
            $parts = explode('||', $result);
            $this->currencyName = $parts[0];
        }

        return $this->currencyName;
    }
}
