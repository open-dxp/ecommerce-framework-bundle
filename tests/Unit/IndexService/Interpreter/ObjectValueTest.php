<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Unit\IndexService\Interpreter;

use OpenDxp\Bundle\EcommerceFrameworkBundle\IndexService\Interpreter\ObjectValue;
use OpenDxp\Model\DataObject\Folder;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

it('reads a field of a related object without a locale', function () {
    $folder = new Folder();
    $folder->setKey('bikes');

    $value = (new ObjectValue())->interpret($folder, ['target' => ['fieldname' => 'key']]);

    expect($value)->toBe('bikes');
});

it('needs the name of the field', function () {
    $interpreter = new ObjectValue();

    expect(fn () => $interpreter->interpret(new Folder(), ['target' => ['locale' => 'en']]))
        ->toThrow(MissingOptionsException::class);
});
