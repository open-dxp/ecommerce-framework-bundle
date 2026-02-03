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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Command\Voucher;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class CleanupStatisticsCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('ecommerce:voucher:cleanup-statistics');
        $this->setDescription('House keeping for Voucher Usage Statistics - cleans up all old data.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output->writeln('<comment>*</comment> Cleaning up <info>statistics</info>');
        Factory::getInstance()->getVoucherService()->cleanUpStatistics();

        return 0;
    }
}
