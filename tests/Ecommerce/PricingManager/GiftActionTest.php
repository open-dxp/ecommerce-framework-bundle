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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Ecommerce\PricingManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Action\Gift;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\CartAmount;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Ecommerce\PricingManager\Rule\AbstractRuleTest;

class GiftActionTest extends AbstractRuleTest
{
    protected array $productDefinitions1 = [
        'singleProduct' => [
            'id' => 4,
            'price' => 100,
        ],
        'cart' => [
            [
                'id' => 4,
                'price' => 100,
            ],
            [
                'id' => 5,
                'price' => 40,
            ],
        ],

    ];

    protected array $tests1 = [
        'productPriceSingle' => 100,
        'productPriceTotal' => 200,
        'cartSubTotal' => 140,
        'cartGrandTotal' => 140,
        'cartSubTotalModificators' => 140,
        'cartGrandTotalModificators' => 150,
        'giftItemCount' => 0,
    ];

    protected array $productDefinitions2 = [
        'singleProduct' => [
            'id' => 4,
            'price' => 100,
        ],
        'cart' => [
            [
                'id' => 4,
                'price' => 100,
            ],
            [
                'id' => 5,
                'price' => 40,
            ],
            [
                'id' => 6,
                'price' => 80,
            ],
        ],
    ];

    public function testOneGift(): void
    {
        $pricingManager = $this->buildPricingManager([]);
        $gift1 = $this->setUpProduct(777, 100, $pricingManager);

        $ruleDefinitions = [
            'testrule' => [
                'actions' => [
                    [
                        'class' => Gift::class,
                        'product' => $gift1,
                    ],
                ],
                'condition' => [
                    'class' => CartAmount::class,
                    'limit' => 200,
                ],
            ],
        ];

        $this->doAssertionsWithShippingCosts($ruleDefinitions, $this->productDefinitions1, $this->tests1, false);

        $tests = [
            'productPriceSingle' => 100,
            'productPriceTotal' => 200,
            'cartSubTotal' => 220,
            'cartGrandTotal' => 220,
            'cartSubTotalModificators' => 220,
            'cartGrandTotalModificators' => 230,
            'giftItemCount' => 1,
        ];

        $this->doAssertionsWithShippingCosts($ruleDefinitions, $this->productDefinitions2, $tests, false);
    }

    public function testMultipleGifts(): void
    {
        $pricingManager = $this->buildPricingManager([]);
        $gift1 = $this->setUpProduct(777, 100, $pricingManager);
        $gift2 = $this->setUpProduct(888, 200, $pricingManager);

        $ruleDefinitions = [
            'testrule' => [
                'actions' => [
                    [
                        'class' => Gift::class,
                        'product' => $gift1,
                    ],
                    [
                        'class' => Gift::class,
                        'product' => $gift2,
                    ],
                ],
                'condition' => [
                    'class' => CartAmount::class,
                    'limit' => 200,
                ],
            ],
        ];

        $this->doAssertionsWithShippingCosts($ruleDefinitions, $this->productDefinitions1, $this->tests1, false);

        $tests = [
            'productPriceSingle' => 100,
            'productPriceTotal' => 200,
            'cartSubTotal' => 220,
            'cartGrandTotal' => 220,
            'cartSubTotalModificators' => 220,
            'cartGrandTotalModificators' => 230,
            'giftItemCount' => 2,
        ];

        $this->doAssertionsWithShippingCosts($ruleDefinitions, $this->productDefinitions2, $tests, false);
    }
}
