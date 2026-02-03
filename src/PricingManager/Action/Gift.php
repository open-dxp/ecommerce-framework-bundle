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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractProduct;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ActionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;

class Gift implements GiftInterface
{
    protected ?AbstractProduct $product = null;

    protected string $productPath = '';

    public function executeOnCart(EnvironmentInterface $environment): GiftInterface
    {
        $comment = $environment->getRule()->getDescription();
        $environment->getCart()->addGiftItem($this->getProduct(), 1, null, true, [], [], $comment);

        return $this;
    }

    /**
     * set gift product
     */
    public function setProduct(AbstractProduct $product): GiftInterface
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct(): ?AbstractProduct
    {
        return $this->product;
    }

    public function toJSON(): string
    {
        return json_encode([
            'type' => 'Gift',
            'product' => $this->getProduct() ? $this->getProduct()->getFullPath() : null,
        ]);
    }

    public function fromJSON(string $string): ActionInterface
    {
        $json = json_decode($string);
        $product = AbstractProduct::getByPath($json->product);

        if ($product) {
            $this->setProduct($product);
        }

        return $this;
    }

    /**
     * dont cache the entire product object
     *
     *
     * @internal
     */
    public function __sleep(): array
    {
        if (is_object($this->product)) {
            $this->productPath = $this->product->getFullPath();
        }

        return ['productPath'];
    }

    /**
     * restore product
     *
     * @internal
     */
    public function __wakeup(): void
    {
        if ($this->productPath !== '') {
            $this->product = AbstractProduct::getByPath($this->productPath);
        }
    }
}
