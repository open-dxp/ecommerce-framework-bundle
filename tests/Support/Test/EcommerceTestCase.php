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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Support\Test;

use OpenDxp;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Environment;
use OpenDxp\Bundle\EcommerceFrameworkBundle\EnvironmentInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\EventListener\SessionBagListener;
use OpenDxp\Localization\LocaleService;
use OpenDxp\Tests\Support\Test\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

abstract class EcommerceTestCase extends TestCase
{
    private ?EnvironmentInterface $environment = null;

    private ?SessionInterface $session = null;

    protected function buildEnvironment(): EnvironmentInterface
    {
        if (null === $this->environment) {
            $this->environment = new Environment(new LocaleService());
        }

        return $this->environment;
    }

    protected function buildSession(): SessionInterface
    {
        if (null === $this->session) {
            $this->session = new Session(new MockArraySessionStorage());

            $configurator = new SessionBagListener();
            $configurator->configure($this->session);

            $this->session->getBag(SessionBagListener::ATTRIBUTE_BAG_CART)->set('carts', []);

            $requestStack = OpenDxp::getContainer()->get('request_stack');
            if (!$request = $requestStack->getCurrentRequest()) {
                $request = new Request();
                $requestStack->push($request);
            }

            $request->setSession($this->session);
        }

        return $this->session;
    }
}
