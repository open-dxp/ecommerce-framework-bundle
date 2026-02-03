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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CartManager\CartInterface ;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\UnsupportedException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\VoucherServiceException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractVoucherSeries;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition\VoucherToken;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\PricingManager;
use OpenDxp\Bundle\EcommerceFrameworkBundle\VoucherService\Token as VoucherServiceToken;
use OpenDxp\Localization\LocaleServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultService implements VoucherServiceInterface
{
    protected int $reservationMinutesThreshold;

    protected int $statisticsDaysThreshold;

    protected ?string $currentLocale = null;

    public function __construct(LocaleServiceInterface $localeService, array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->processOptions($resolver->resolve($options));

        $this->currentLocale = $localeService->getLocale();
    }

    protected function processOptions(array $options): void
    {
        $this->reservationMinutesThreshold = $options['reservation_minutes_threshold'];
        $this->statisticsDaysThreshold = $options['statistics_days_threshold'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'reservation_minutes_threshold',
            'statistics_days_threshold',
        ]);

        $resolver->setDefaults([
            'reservation_minutes_threshold' => 5,
            'statistics_days_threshold' => 30,
        ]);

        $resolver->setAllowedTypes('reservation_minutes_threshold', 'int');
        $resolver->setAllowedTypes('statistics_days_threshold', 'int');
    }

    /**
     * @throws VoucherServiceException
     */
    public function checkToken(string $code, CartInterface $cart): bool
    {
        if ($tokenManager = $this->getTokenManager($code)) {
            return $tokenManager->checkToken($code, $cart);
        }

        throw new VoucherServiceException('No Token for code ' .$code . ' exists.', VoucherServiceException::ERROR_CODE_NO_TOKEN_FOR_THIS_CODE_EXISTS);
    }

    public function reserveToken(string $code, CartInterface $cart): bool
    {
        if ($tokenManager = $this->getTokenManager($code)) {
            return $tokenManager->reserveToken($code, $cart);
        }

        return false;
    }

    public function releaseToken(string $code, CartInterface $cart): bool
    {
        if ($tokenManager = $this->getTokenManager($code)) {
            return $tokenManager->releaseToken($code, $cart);
        }

        return false;
    }

    public function applyToken(string $code, CartInterface $cart, AbstractOrder $order): bool
    {
        if (($tokenManager = $this->getTokenManager($code)) && $orderToken = $tokenManager->applyToken($code, $cart, $order)) {
            $voucherTokens = $order->getVoucherTokens();
            $voucherTokens[] = $orderToken;
            $order->setVoucherTokens($voucherTokens);
            $this->releaseToken($code, $cart);

            return true;
        }

        return false;
    }

    /**
     * Gets the correct token manager and calls removeAppliedTokenFromOrder(), which cleans up the
     * token usage and the ordered token object if necessary, removes the token object from the order.
     */
    public function removeAppliedTokenFromOrder(\OpenDxp\Model\DataObject\OnlineShopVoucherToken $tokenObject, AbstractOrder $order): bool
    {
        /** @var AbstractVoucherSeries $series */
        $series = $tokenObject->getVoucherSeries();

        if ($tokenManager = $series->getTokenManager()) {
            $tokenManager->removeAppliedTokenFromOrder($tokenObject, $order);

            $voucherTokens = $order->getVoucherTokens();

            $newVoucherTokens = [];
            foreach ($voucherTokens as $voucherToken) {
                if ($voucherToken->getId() != $tokenObject->getId()) {
                    $newVoucherTokens[] = $voucherToken;
                }
            }

            $order->setVoucherTokens($newVoucherTokens);

            return true;
        }

        return false;
    }

    /**
     * @return PricingManagerTokenInformation[]
     *
     * @throws UnsupportedException
     */
    public function getPricingManagerTokenInformationDetails(CartInterface $cart, ?string $locale = null): array
    {
        if (empty($cart->getVoucherTokenCodes())) {
            return [];
        }

        if (null == $locale) {
            $locale = $this->currentLocale;
        }

        // get all valid rules configured in system
        /** @var PricingManager $pricingManager */
        $pricingManager = Factory::getInstance()->getPricingManager();
        $validRules = $pricingManager->getValidRules();
        $validRulesAssoc = [];
        foreach ($validRules as $rule) {
            $validRulesAssoc[$rule->getId()] = $rule;
        }

        // get all applied rules for current cart and cart items
        $appliedRules = $cart->getPriceCalculator()->getAppliedPricingRules();

        // filter applied rules for voucher conditions
        $appliedRulesWithVoucherCondition = [];
        foreach ($appliedRules as $appliedRule) {
            $conditions = $appliedRule->getConditionsByType(VoucherToken::class);
            if ($conditions) {
                $appliedRulesWithVoucherCondition[$appliedRule->getId()] = $conditions;
            }
        }

        // calculate not applied rules with voucher conditions
        $notAppliedRules = array_udiff($validRules, $appliedRules, fn ($rule1, $rule2) => $rule1->getId() <=> $rule2->getId());
        $notAppliedRulesWithVoucherCondition = [];
        foreach ($notAppliedRules as $notAppliedRule) {
            $conditions = $notAppliedRule->getConditionsByType(VoucherToken::class);
            if ($conditions) {
                $notAppliedRulesWithVoucherCondition[$notAppliedRule->getId()] = $conditions;
            }
        }

        $tokenInformationList = [];

        foreach ($cart->getVoucherTokenCodes() as $tokenCode) {
            $tokenInformation = new PricingManagerTokenInformation();
            $tokenInformation->setTokenCode($tokenCode);
            $tokenInformation->setTokenObject(VoucherServiceToken::getByCode($tokenCode));

            $notAppliedPricingRules = [];
            $appliedPricingRules = [];
            $errorMessages = [];

            foreach ($notAppliedRulesWithVoucherCondition as $ruleId => $conditions) {
                foreach ($conditions as $condition) {
                    /** @var VoucherToken $condition */
                    if ($condition->checkVoucherCode($tokenCode)) {
                        $errorMessages[] = $condition->getErrorMessage($locale);
                        $notAppliedPricingRules[] = $validRulesAssoc[$ruleId];
                    }
                }
            }

            if (!$errorMessages) {
                $hasRule = false;
                foreach ($appliedRulesWithVoucherCondition as $ruleId => $conditions) {
                    foreach ($conditions as $condition) {
                        /** @var VoucherToken $condition */
                        if ($condition->checkVoucherCode($tokenCode)) {
                            $hasRule = true;
                            $appliedPricingRules[] = $validRulesAssoc[$ruleId];

                            break;
                        }
                    }
                }

                $tokenInformation->setHasNoValidRule(!$hasRule);
            }

            $tokenInformation->setErrorMessages($errorMessages);
            $tokenInformation->setNotAppliedRules($notAppliedPricingRules);
            $tokenInformation->setAppliedRules($appliedPricingRules);

            $tokenInformationList[$tokenCode] = $tokenInformation;
        }

        return $tokenInformationList;
    }

    public function cleanUpReservations(?int $seriesId = null): bool
    {
        return Reservation::cleanUpReservations($this->reservationMinutesThreshold, $seriesId);
    }

    public function cleanUpVoucherSeries(\OpenDxp\Model\DataObject\OnlineShopVoucherSeries $series): bool
    {
        return Token\Listing::cleanUpAllTokens($series->getId());
    }

    public function cleanUpStatistics(?int $seriesId = null): bool
    {
        if (isset($seriesId)) {
            return Statistic::cleanUpStatistics($this->statisticsDaysThreshold, $seriesId);
        }

        return Statistic::cleanUpStatistics($this->statisticsDaysThreshold);
    }

    public function getTokenManager(string $code): ?TokenManager\TokenManagerInterface
    {
        if (!$token = Token::getByCode($code)) {
            return null;
        }
        if ($series = \OpenDxp\Model\DataObject\OnlineShopVoucherSeries::getById($token->getVoucherSeriesId())) {
            return $series->getTokenManager();
        }

        return null;
    }
}
