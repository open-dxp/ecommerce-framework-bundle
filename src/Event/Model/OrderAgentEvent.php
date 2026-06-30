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

use OpenDxp\Bundle\EcommerceFrameworkBundle\OrderManager\OrderAgentInterface;
use OpenDxp\Event\Traits\ArgumentsAwareTrait;
use Symfony\Contracts\EventDispatcher\Event;

class OrderAgentEvent extends Event
{
    use ArgumentsAwareTrait;

    /**
     * OrderAgentEvent constructor.
     */
    public function __construct(
        protected OrderAgentInterface $orderAgent,
        array $arguments = []
    ) {
        $this->arguments = $arguments;
    }

    public function getOrderAgent(): OrderAgentInterface
    {
        return $this->orderAgent;
    }

    public function setOrderAgent(OrderAgentInterface $orderAgent): void
    {
        $this->orderAgent = $orderAgent;
    }
}
