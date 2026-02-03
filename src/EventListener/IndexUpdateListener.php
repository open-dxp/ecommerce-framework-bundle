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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\EventListener;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Factory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;
use OpenDxp\Event\DataObjectEvents;
use OpenDxp\Event\Model\DataObjectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class IndexUpdateListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::POST_ADD => 'onObjectUpdate',
            DataObjectEvents::POST_UPDATE => 'onObjectUpdate',
            DataObjectEvents::PRE_DELETE => 'onObjectDelete',
        ];
    }

    public function onObjectUpdate(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if ($object instanceof IndexableInterface && (!$event->hasArgument('saveVersionOnly') || !$event->getArgument('saveVersionOnly'))) {
            $indexService = Factory::getInstance()->getIndexService();
            $indexService->updateIndex($object);
        }
    }

    public function onObjectDelete(DataObjectEvent $event): void
    {
        $object = $event->getObject();

        if ($object instanceof IndexableInterface) {
            $indexService = Factory::getInstance()->getIndexService();
            $indexService->deleteFromIndex($object);
        }

        // Delete tokens when a a configuration object gets removed.
        if ($object instanceof \OpenDxp\Model\DataObject\OnlineShopVoucherSeries) {
            $voucherService = Factory::getInstance()->getVoucherService();
            $voucherService->cleanUpVoucherSeries($object);
        }
    }
}
