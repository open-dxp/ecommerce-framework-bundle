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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\SynonymProvider;

use Exception;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Traits\OptionsResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FileSynonymProvider extends AbstractSynonymProvider implements SynonymProviderInterface
{
    use OptionsResolverTrait;

    const SYNONYM_FILE_OPTION = 'synonymFile';

    public function getSynonyms(): array
    {
        $options = $this->resolveOptions($this->getOptions());
        $filePath = $options[static::SYNONYM_FILE_OPTION];
        if (!file_exists($filePath)) {
            throw new Exception(sprintf('File "%s" does not exist on the local system. Please verify the path.', $filePath));
        }

        $content = file_get_contents($filePath);

        return explode_and_trim(PHP_EOL, $content);
    }

    protected function configureOptionsResolver(string $resolverName, OptionsResolver $resolver): void
    {
        $resolver->setRequired(static::SYNONYM_FILE_OPTION);
    }
}
