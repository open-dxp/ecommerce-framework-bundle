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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Getter;

use OpenDxp\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use OpenDxp\Model\Asset;
use OpenDxp\Model\Document;
use OpenDxp\Model\Element\Tag;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagsGetter implements GetterInterface
{
    use OptionsResolverTrait;

    public function get(object $object, ?array $config = null): mixed
    {
        $config = $this->resolveOptions($config ?? []);

        $type = 'object';
        if ($object instanceof Asset) {
            $type = 'asset';
        } elseif ($object instanceof Document) {
            $type = 'document';
        }

        $tags = Tag::getTagsForElement($type, $object->getId());

        if (!$config['includeParentTags']) {
            return $tags;
        }

        $result = [];
        foreach ($tags as $tag) {
            $result[] = $tag;

            $parent = $tag->getParent();
            while ($parent instanceof Tag) {
                $result[] = $parent;
                $parent = $parent->getParent();
            }
        }

        return $result;
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'includeParentTags' => false,
        ]);

        $resolver->setAllowedTypes('includeParentTags', 'bool');
    }
}
