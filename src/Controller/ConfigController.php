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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Controller;

use OpenDxp\Controller\KernelControllerEventInterface;
use OpenDxp\Controller\UserAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ConfigController
 *
 * @internal
 */
#[Route('/config')]
class ConfigController extends UserAwareController implements KernelControllerEventInterface
{
    /**
     * ConfigController constructor.
     */
    public function __construct(private RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelControllerEvent(ControllerEvent $event): void
    {
        $this->checkPermission('bundle_ecommerce_back-office_order');
    }

    #[Route('/js-config', name: 'opendxp_ecommerceframework_config_jsconfig', methods: ['GET'])]
    public function jsConfigAction(): Response
    {
        $config = $this->getParameter('opendxp_ecommerce.opendxp.config');

        $orderList = $config['menu']['order_list'];
        if ($orderList['route']) {
            $orderList['route'] = $this->router->generate($orderList['route']);
        } elseif ($orderList['path']) {
            $orderList['route'] = $orderList['path'];
        }

        unset($orderList['path']);

        $config['menu']['order_list'] = $orderList;

        $javascript = 'opendxp.registerNS("opendxp.bundle.EcommerceFramework.config");' . PHP_EOL;

        $javascript .= 'opendxp.bundle.EcommerceFramework.config = ';
        $javascript .= json_encode($config) . ';';

        $response = new Response($javascript);
        $response->headers->set('Content-Type', 'application/javascript');

        return $response;
    }
}
