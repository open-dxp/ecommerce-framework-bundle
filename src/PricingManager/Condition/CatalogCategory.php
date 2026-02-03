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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\Condition;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractCategory;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\ConditionInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\PricingManager\EnvironmentInterface;

class CatalogCategory extends AbstractObjectListCondition implements CategoryInterface
{
    /**
     * @var AbstractCategory[]
     */
    protected array $categories = [];

    /**
     * Serialized category IDs
     */
    protected array $categoryIds = [];

    /**
     * @param AbstractCategory[] $categories
     */
    public function setCategories(array $categories): CategoryInterface
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return AbstractCategory[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function toJSON(): string
    {
        // basic
        $json = [
            'type' => 'CatalogCategory',
            'categories' => [],
        ];

        // add categories
        foreach ($this->getCategories() as $category) {
            $json['categories'][] = [
                $category->getId(),
                $category->getFullPath(),
            ];
        }

        return json_encode($json);
    }

    public function fromJSON(string $string): ConditionInterface
    {
        $json = json_decode($string);

        $categories = [];
        foreach ($json->categories as $cat) {
            $category = $this->loadObject($cat->id);
            if ($category) {
                $categories[] = $category;
            }
        }
        $this->setCategories($categories);

        return $this;
    }

    /**
     * Don't cache the entire category object
     *
     *
     * @internal
     */
    public function __sleep(): array
    {
        return $this->handleSleep('categories', 'categoryIds');
    }

    /**
     * Restore categories from serialized ID list
     *
     * @internal
     */
    public function __wakeup(): void
    {
        $this->handleWakeup('categories', 'categoryIds');
    }

    public function check(EnvironmentInterface $environment): bool
    {
        foreach ($environment->getCategories() as $category) {
            foreach ($this->getCategories() as $allow) {
                if (str_contains($category->getFullPath(), $allow->getFullPath())) {
                    return true;
                }
            }
        }

        return false;
    }
}
