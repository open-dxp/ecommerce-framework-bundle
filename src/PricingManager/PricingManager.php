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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartPriceModificator\Discount;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\CheckoutableInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PriceSystem\PriceInfoInterface as PriceSystemPriceInfoInterface;
use OpenDxp\Bundle\PersonalizationBundle\Targeting\VisitorInfoStorageInterface;
use RuntimeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PricingManager implements PricingManagerInterface
{
    protected bool $enabled = true;

    protected array $options;

    /**
     * @var RuleInterface[]|null
     */
    protected ?array $rules = null;

    public function __construct(
        /**
         * Condition name => class mapping
         */
        protected array $conditionMapping,
        /**
         * Action name => class mapping
         */
        protected array $actionMapping,
        array $options = [],
        protected ?VisitorInfoStorageInterface $visitorInfoStorage = null
    ) {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $classProperties = ['rule_class', 'price_info_class', 'environment_class'];

        $resolver->setRequired($classProperties);

        $resolver->setDefaults([
            'rule_class' => Rule::class,
            'price_info_class' => PriceInfo::class,
            'environment_class' => Environment::class,
        ]);

        foreach ($classProperties as $classProperty) {
            $resolver->setAllowedTypes($classProperty, 'string');
        }
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function applyProductRules(PriceSystemPriceInfoInterface $priceInfo): PriceInfoInterface|PriceSystemPriceInfoInterface
    {
        if (!$this->enabled) {
            return $priceInfo;
        }

        // create new price info with pricing rules
        $priceInfoWithRules = $this->getPriceInfo($priceInfo);

        // add all valid rules to the price info
        foreach ($this->getValidRules() as $rule) {
            $priceInfoWithRules->addRule($rule);
        }

        return $priceInfoWithRules;
    }

    /**
     * @return RuleInterface[]
     */
    public function applyCartRules(CartInterface $cart): array
    {
        $appliedRules = [];

        if (!$this->enabled) {
            return $appliedRules;
        }

        // configure environment
        $env = $this->getEnvironment();
        $env->setCart($cart);
        $env->setExecutionMode(EnvironmentInterface::EXECUTION_MODE_CART);
        $env->setProduct(null);
        if ($this->visitorInfoStorage && $this->visitorInfoStorage->hasVisitorInfo()) {
            $env->setVisitorInfo($this->visitorInfoStorage->getVisitorInfo());
        }

        $categories = [];
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();
            if (method_exists($product, 'getCategories')) {
                $productCategories = $product->getCategories();
                if (is_array($productCategories)) {
                    foreach ($productCategories as $c) {
                        $categories[$c->getId()] = $c;
                    }
                }
            }
        }

        $env->setCategories(array_values($categories));

        // clean up discount pricing modificators in cart price calculator
        $priceCalculator = $cart->getPriceCalculator();
        $priceModificators = $priceCalculator->getModificators();

        foreach ($priceModificators as $priceModificator) {
            if ($priceModificator instanceof Discount) {
                $priceCalculator->removeModificator($priceModificator);
            }
        }

        // execute all valid rules
        foreach ($this->getValidRules() as $rule) {
            $env->setRule($rule);

            // test rule
            if ($rule->check($env) === false) {
                continue;
            }

            // execute rule
            $rule->executeOnCart($env);
            $appliedRules[] = $rule;

            // is this a stop rule?
            if ($rule->getBehavior() === Rule::ATTRIBUTE_BEHAVIOR_LASTRULE) {
                break;
            }
        }

        return $appliedRules;
    }

    /**
     * @return RuleInterface[]
     */
    public function getValidRules(): array
    {
        if (is_null($this->rules)) {
            $rules = $this->getRuleListing();
            $rules->setCondition('active = 1');
            $rules->setOrderKey('prio');
            $rules->setOrder('ASC');

            $rules->getDao()->setRuleClass($this->options['rule_class']);

            $this->rules = $rules->getRules();
        }

        return $this->rules;
    }

    public function getEnvironment(): EnvironmentInterface
    {
        $class = $this->options['environment_class'];

        /** @var EnvironmentInterface $environment */
        $environment = new $class();

        return $environment;
    }

    public function getRuleListing(): Rule\Listing
    {
        $class = $this->options['rule_class'] . '\\Listing';

        return new $class;
    }

    public function getConditionMapping(): array
    {
        return $this->conditionMapping;
    }

    public function getActionMapping(): array
    {
        return $this->actionMapping;
    }

    /**
     * Factory
     *
     *
     *
     * @throws InvalidConfigException
     */
    public function getCondition(string $type): ConditionInterface
    {
        if (!isset($this->conditionMapping[$type])) {
            throw new InvalidConfigException(sprintf('ConditionInterface for type "%s" is not registered', $type));
        }

        $class = $this->conditionMapping[$type];

        return new $class();
    }

    /**
     * Factory
     *
     *
     *
     * @throws InvalidConfigException
     */
    public function getAction(string $type): ActionInterface
    {
        if (!isset($this->actionMapping[$type])) {
            throw new InvalidConfigException(sprintf('ActionInterface for type "%s" is not registered', $type));
        }

        $class = $this->actionMapping[$type];

        return new $class();
    }

    /**
     * @throws InvalidConfigException
     */
    public function getPriceInfo(PriceSystemPriceInfoInterface $priceInfo): PriceInfoInterface
    {
        // TODO make getPriceInfo private as this call is only used internally where the enabled check is alread applied?
        if (!$this->enabled) {
            throw new RuntimeException('Can\'t build a pricing manager price info as the pricing manager is disabled');
        }

        $class = $this->options['price_info_class'];

        // create environment
        $environment = $this->getEnvironment();
        $environment->setProduct($priceInfo->getProduct());

        if ($priceInfo->getProduct() && method_exists($priceInfo->getProduct(), 'getCategories')) {
            $environment->setCategories((array)$priceInfo->getProduct()->getCategories());
        }

        if ($this->visitorInfoStorage && $this->visitorInfoStorage->hasVisitorInfo()) {
            $environment->setVisitorInfo($this->visitorInfoStorage->getVisitorInfo());
        }

        $priceInfoWithRules = new $class($priceInfo, $environment);
        $environment->setPriceInfo($priceInfoWithRules);

        return $priceInfoWithRules;
    }
}
