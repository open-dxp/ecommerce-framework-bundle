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

use OpenDxp\Bundle\EcommerceFrameworkBundle\CheckoutManager\CommitOrderProcessorInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractOrder;
use OpenDxp\Event\Traits\ArgumentsAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class SendConfirmationMailEvent extends Event
{
    use ArgumentsAwareTrait;

    protected bool $skipDefaultBehaviour = false;

    /**
     * SendConfirmationMailEvent constructor.
     */
    public function __construct(
        protected CommitOrderProcessorInterface $commitOrderProcessor,
        protected AbstractOrder $order,
        protected string $confirmationMailConfig
    ) {
    }

    public function getCommitOrderProcessor(): CommitOrderProcessorInterface
    {
        return $this->commitOrderProcessor;
    }

    public function setCommitOrderProcessor(CommitOrderProcessorInterface $commitOrderProcessor): void
    {
        $this->commitOrderProcessor = $commitOrderProcessor;
    }

    public function getOrder(): AbstractOrder
    {
        return $this->order;
    }

    public function setOrder(AbstractOrder $order): void
    {
        $this->order = $order;
    }

    public function getConfirmationMailConfig(): string
    {
        return $this->confirmationMailConfig;
    }

    public function setConfirmationMailConfig(string $confirmationMailConfig): void
    {
        $this->confirmationMailConfig = $confirmationMailConfig;
    }

    public function doSkipDefaultBehaviour(): bool
    {
        return $this->skipDefaultBehaviour;
    }

    public function setSkipDefaultBehaviour(bool $skipDefaultBehaviour): void
    {
        $this->skipDefaultBehaviour = $skipDefaultBehaviour;
    }
}
