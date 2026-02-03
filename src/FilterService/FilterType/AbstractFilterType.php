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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\FilterService\FilterType;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Exception\InvalidConfigException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\ProductList\ProductListInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\AbstractFilterDefinitionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractFilterType
{
    const EMPTY_STRING = '$$EMPTY$$';

    protected Environment $twig;

    protected ?Request $request = null;

    /**
     * @param string $template for rendering the filter frontend
     * @param array $options for additional options
     */
    public function __construct(
        protected TranslatorInterface $translator,
        Environment                   $twig,
        RequestStack                  $requestStack,
        protected string              $template,
        array                         $options = []
    ) {
        $this->twig = $twig;
        $this->request = $requestStack->getCurrentRequest();

        $this->processOptions($options);
    }

    protected function processOptions(array $options): void
    {
        // noop - to implemented by filter types supporting options
    }

    protected function getField(AbstractFilterDefinitionType $filterDefinition): string|IndexFieldSelection|null
    {
        $field = $filterDefinition->getField();
        if ($field instanceof IndexFieldSelection) {
            return $field->getField();
        }

        return $field;
    }

    protected function getTemplate(AbstractFilterDefinitionType $filterDefinition): ?string
    {
        if (!empty($filterDefinition->getScriptPath())) {
            return $filterDefinition->getScriptPath();
        }
        return $this->template;
    }

    protected function getPreSelect(AbstractFilterDefinitionType $filterDefinition): array|string|int|null
    {
        $field = $filterDefinition->getField();
        if ($field instanceof IndexFieldSelection) {
            return $field->getPreSelect();
        }
        if (method_exists($filterDefinition, 'getPreSelect')) {
            return $filterDefinition->getPreSelect();
        }

        return null;
    }

    /**
     * renders and returns the rendered html snippet for the current filter
     * based on settings in the filter definition and the current filter params.
     */
    public function getFilterFrontend(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): string
    {
        return $this->render(
            $this->getTemplate($filterDefinition),
            $this->getFilterValues($filterDefinition, $productList, $currentFilter)
        );
    }

    /**
     * returns the raw data for the current filter based on settings in the
     * filter definition and the current filter params.
     */
    abstract public function getFilterValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter): array;

    /**
     * adds necessary conditions to the product list implementation based on the currently set filter params.
     *
     *
     *
     *
     * @throws InvalidConfigException
     */
    abstract public function addCondition(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList, array $currentFilter, array $params, bool $isPrecondition = false): array;

    /**
     * calls prepareGroupByValues of productlist if necessary
     */
    public function prepareGroupByValues(AbstractFilterDefinitionType $filterDefinition, ProductListInterface $productList): void
    {
        //by default do thing here
    }

    /**
     * sort result
     */
    protected function sortResult(AbstractFilterDefinitionType $filterDefinition, array $result): array
    {
        return $result;
    }

    /**
     * renders filter template
     */
    protected function render(string $template, array $parameters = []): string
    {
        return $this->twig->render($template, $parameters);
    }
}
