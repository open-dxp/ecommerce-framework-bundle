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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\Definition;

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Config\ConfigInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Getter\ExtendedGetterInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Interpreter\InterpreterInterface;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Model\IndexableInterface;

class Attribute
{
    private readonly string $fieldName;

    public function __construct(
        private readonly string $name,
        string $fieldName = null,
        private readonly ?string $type = null,
        private readonly ?string $locale = null,
        private readonly ?string $filterGroup = null,
        private array $options = [],
        private readonly ?GetterInterface $getter = null,
        private readonly array $getterOptions = [],
        private readonly ?InterpreterInterface $interpreter = null,
        private readonly array $interpreterOptions = [],
        private readonly bool $hideInFieldlistDatatype = false
    ) {
        $this->fieldName = $fieldName ?? $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getFilterGroup(): ?string
    {
        return $this->filterGroup;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name, mixed $defaultValue = null): mixed
    {
        return $this->options[$name] ?? $defaultValue;
    }

    public function getGetter(): ?GetterInterface
    {
        return $this->getter;
    }

    public function getGetterOptions(): array
    {
        return $this->getterOptions;
    }

    public function getInterpreter(): ?InterpreterInterface
    {
        return $this->interpreter;
    }

    public function getInterpreterOptions(): array
    {
        return $this->interpreterOptions;
    }

    public function getHideInFieldlistDatatype(): bool
    {
        return $this->hideInFieldlistDatatype;
    }

    /**
     * Get value from object, running through getter if defined
     */
    public function getValue(IndexableInterface $object, int $subObjectId = null, ConfigInterface $tenantConfig = null, mixed $default = null): mixed
    {
        if ($this->getter instanceof \OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Getter\GetterInterface) {
            if ($this->getter instanceof ExtendedGetterInterface) {
                return $this->getter->get($object, $this->getterOptions, $subObjectId, $tenantConfig);
            }
            return $this->getter->get($object, $this->getterOptions);
        }

        $getter = 'get' . ucfirst($this->fieldName);
        if (method_exists($object, $getter)) {
            return $object->$getter($this->locale);
        }

        return $default;
    }

    /**
     * Interpret value with interpreter if defined
     */
    public function interpretValue(mixed $value): mixed
    {
        if ($this->interpreter instanceof \OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Interpreter\InterpreterInterface) {
            return $this->interpreter->interpret($value, $this->interpreterOptions);
        }

        return $value;
    }
}
