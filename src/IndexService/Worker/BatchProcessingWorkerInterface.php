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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Worker;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;

/**
 * Interface for IndexService workers which support batch processing of index data preparation and index updating
 */
interface BatchProcessingWorkerInterface extends WorkerInterface
{
    /**
     * fills queue based on path
     */
    public function fillupPreparationQueue(IndexableInterface $object): void;

    /**
     * prepare data for index creation and store is in store table
     *
     *
     * @return array returns the processed subobjects that can be used for the index update.
     */
    public function prepareDataForIndex(IndexableInterface $object): array;

    /**
     * resets the store table by marking all items as "in preparation", so items in store will be regenerated
     */
    public function resetPreparationQueue(): void;

    /**
     * resets the store table to initiate a re-indexing
     */
    public function resetIndexingQueue(): void;
}
