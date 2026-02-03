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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager;

use Exception;
use OpenDxp;
use OpenDxp\Bundle\EcommerceFrameworkBundle\EventListener\SessionBagListener;
use Override;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

class SessionCart extends AbstractCart implements CartInterface
{
    /**
     * @var SessionCart[]|null
     */
    protected static ?array $unserializedCarts = null;

    protected function getCartItemClassName(): string
    {
        return SessionCartItem::class;
    }

    protected function getCartCheckoutDataClassName(): string
    {
        return SessionCartCheckoutData::class;
    }

    protected static function getSessionBag(): AttributeBagInterface
    {
        try {
            $session = OpenDxp::getContainer()->get('request_stack')->getSession();
        } catch (SessionNotFoundException) {
            trigger_deprecation('open-dxp/opendxp', '1.0',
                sprintf('Session used with non existing request stack in %s, that will not be possible in OpenDXP 1.', self::class));

            $session = OpenDxp::getContainer()->get('session');
        }

        /** @var AttributeBagInterface $sessionBag */
        $sessionBag = $session->getBag(SessionBagListener::ATTRIBUTE_BAG_CART);

        if (empty($sessionBag->get('carts'))) {
            $sessionBag->set('carts', []);
        }

        return $sessionBag;
    }

    public function save(): void
    {
        $session = static::getSessionBag();

        if (!$this->getId()) {
            $this->setId(uniqid('sesscart_'));
        }

        $carts = $session->get('carts');
        $carts[$this->getId()] = serialize($this);

        $session->set('carts', $carts);
    }

    /**
     * @throws Exception if the cart is not yet saved.
     */
    public function delete(): void
    {
        $session = static::getSessionBag();

        if (!$this->getId()) {
            throw new Exception('Cart saved not yet.');
        }

        $this->clear();

        $carts = $session->get('carts');
        unset($carts[$this->getId()]);

        $session->set('carts', $carts);
    }

    #[Override]
    public function sortItems(callable $value_compare_func): static
    {
        if (is_array($this->items)) {
            uasort($this->items, $value_compare_func);
        }

        return $this;
    }

    public static function getById(int|string $id): ?SessionCart
    {
        $carts = static::getAllCartsForUser(-1);

        return $carts[$id] ?? null;
    }

    /**
     * @static
     *
     * @return SessionCart[]
     */
    public static function getAllCartsForUser(int $userId): array
    {
        if (null === static::$unserializedCarts) {
            static::$unserializedCarts = [];

            foreach (static::getSessionBag()->get('carts') as $serializedCart) {
                $cart = unserialize($serializedCart);
                static::$unserializedCarts[$cart->getId()] = $cart;
            }
        }

        return static::$unserializedCarts;
    }

    /**
     * @internal
     */
    #[Override]
    public function __sleep(): array
    {
        $vars = parent::__sleep();

        $blockedVars = ['creationDate', 'modificationDate', 'priceCalculator'];

        $finalVars = [];
        foreach ($vars as $key) {
            if (!in_array($key, $blockedVars)) {
                $finalVars[] = $key;
            }
        }

        return $finalVars;
    }

    /**
     * modified flag needs to be set
     *
     * @internal
     */
    public function __wakeup(): void
    {
        $timestampBackup = $this->getModificationDate();

        // set current cart
        foreach ($this->getItems() as $item) {
            $item->setCart($this);

            if ($item->getSubItems()) {
                foreach ($item->getSubItems() as $subItem) {
                    $subItem->setCart($this);
                }
            }
        }
        $this->modified();

        $this->setModificationDate($timestampBackup);
    }
}
