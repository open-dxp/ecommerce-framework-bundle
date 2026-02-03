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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tracking;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

abstract class Tracker implements TrackerInterface
{
    protected string $templatePrefix;

    /**
     * Tracker constructor.
     */
    public function __construct(
        protected TrackingItemBuilderInterface $trackingItemBuilder,
        protected Environment $twig,
        array $options = [],
        protected array $assortmentTenants = [],
        protected array $checkoutTenants = []
    ) {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->processOptions($resolver->resolve($options));
    }

    protected function processOptions(array $options): void
    {
        $this->templatePrefix = $options['template_prefix'];
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['template_prefix']);

        $resolver->setAllowedTypes('template_prefix', 'string');
    }

    protected function getTemplatePath(string $name): string
    {
        return sprintf(
            '%s/%s.js.twig',
            $this->templatePrefix,
            $name
        );
    }

    protected function renderTemplate(string $name, array $parameters): string
    {
        return $this->twig->render(
            $this->getTemplatePath($name),
            $parameters
        );
    }

    /**
     * Remove null values from an object, keep protected keys in any case
     */
    protected function filterNullValues(array $data, array $protectedKeys = []): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $isProtected = in_array($key, $protectedKeys);
            if (null !== $value || $isProtected) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function getAssortmentTenants(): array
    {
        return $this->assortmentTenants;
    }

    public function getCheckoutTenants(): array
    {
        return $this->checkoutTenants;
    }
}
