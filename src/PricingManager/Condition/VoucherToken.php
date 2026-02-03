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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token as VoucherServiceToken;
use OpenDxp\Model\DataObject\Concrete;
use stdClass;

class VoucherToken implements ConditionInterface
{
    /**
     * @var int[]
     */
    protected array $allowListIds = [];

    /**
     * @var stdClass[]
     */
    protected array $allowList = [];

    /**
     * @var string[]
     */
    protected array $errorMessages = [];

    public function check(EnvironmentInterface $environment): bool
    {
        if (!($cart = $environment->getCart())) {
            return false;
        }

        foreach ($cart->getVoucherTokenCodes() as $code) {
            if ($this->checkVoucherCode($code)) {
                return true;
            }
        }

        return false;
    }

    public function checkVoucherCode(string $code): bool
    {
        return in_array(VoucherServiceToken::getByCode($code)?->getVoucherSeriesId(), $this->allowListIds);
    }

    public function toJSON(): string
    {
        // basic
        $json = [
            'type' => 'VoucherToken',
            'allowList' => [],
            'error_messages' => $this->getErrorMessagesRaw(),
        ];

        // add categories
        foreach ($this->getAllowList() as $series) {
            $json['allowList'][] = [
                $series->id,
                $series->path,
            ];
        }

        return json_encode($json);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);

        $allowListIds = [];
        $allowList = [];

        foreach ($json->allowList as $series) {
            $seriesId = $series->id;
            if ($seriesId) {
                $allowListIds[] = $seriesId;
                $allowList[] = $series;
            }
        }

        $this->setErrorMessagesRaw((array)$json->error_messages);

        $this->setAllowListIds($allowListIds);
        $this->setAllowList($allowList);

        return $this;
    }

    protected function loadSeries(int $id): ?Concrete
    {
        return Concrete::getById($id);
    }

    /**
     * @return int[]
     */
    public function getAllowListIds(): array
    {
        return $this->allowListIds;
    }

    /**
     * @param int[] $allowListIds
     */
    public function setAllowListIds(array $allowListIds): void
    {
        $this->allowListIds = $allowListIds;
    }

    /**
     * @return stdClass[]
     */
    public function getAllowList(): array
    {
        return $this->allowList;
    }

    /**
     * @param stdClass[] $allowList
     */
    public function setAllowList(array $allowList): void
    {
        $this->allowList = $allowList;
    }

    /**
     * @return string[]
     */
    public function getErrorMessagesRaw(): array
    {
        return $this->errorMessages;
    }

    /**
     * @param string[] $errorMessages
     */
    public function setErrorMessagesRaw(array $errorMessages): void
    {
        $this->errorMessages = $errorMessages;
    }

    public function getErrorMessage(string $locale): string
    {
        return $this->errorMessages[$locale];
    }
}
