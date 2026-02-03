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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Event\Model;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CheckoutStepInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\V7\CheckoutManagerInterface;
use OpenDxp\Event\Traits\ArgumentsAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class CheckoutManagerStepsEvent extends Event
{
    use ArgumentsAwareTrait;

    protected ?CheckoutStepInterface $currentStep = null;

    protected CheckoutManagerInterface $checkoutManager;

    public function __construct(CheckoutManagerInterface $checkoutManager, ?CheckoutStepInterface $currentStep, array $arguments = [])
    {
        $this->checkoutManager = $checkoutManager;
        $this->currentStep = $currentStep;
        $this->arguments = $arguments;
    }

    public function getCurrentStep(): ?CheckoutStepInterface
    {
        return $this->currentStep;
    }

    public function setCurrentStep(?CheckoutStepInterface $currentStep): void
    {
        $this->currentStep = $currentStep;
    }

    public function getCheckoutManager(): CheckoutManagerInterface
    {
        return $this->checkoutManager;
    }

    public function setCheckoutManager(CheckoutManagerInterface $checkoutManager): void
    {
        $this->checkoutManager = $checkoutManager;
    }
}
