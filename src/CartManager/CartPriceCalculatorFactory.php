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

use OpenDxp\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartPriceCalculatorFactory implements CartPriceCalculatorFactoryInterface
{
    protected EnvironmentInterface $environment;

    protected array $options;

    public function __construct(
        protected array $modificatorConfig,
        array $options = []
    ) {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('class');

        $resolver->setDefaults([
            'class' => CartPriceCalculator::class,
        ]);

        $resolver->setAllowedTypes('class', 'string');
    }

    public function create(EnvironmentInterface $environment, CartInterface $cart): CartPriceCalculatorInterface
    {
        $class = $this->options['class'];

        return new $class($environment, $cart, $this->modificatorConfig);
    }
}
