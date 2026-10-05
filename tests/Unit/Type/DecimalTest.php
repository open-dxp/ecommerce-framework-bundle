<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;

describe('creating a value', function () {
    it('represents a value as a raw integer, a number and a string', function () {
        $value = Decimal::create(10.0, 4);

        expect($value->asRawValue())->toEqual(100000)
            ->and($value->asNumeric())->toEqual(10.0)
            ->and($value->asString())->toBe('10.0000');
    });

    it('creates a value with a scale of four from every kind of input', function (float|int|string|Decimal $input, string $expected) {
        expect(Decimal::create($input)->asString())->toBe($expected);
    })->with([
        'a float' => [15.99, '15.9900'],
        'a string' => ['15.99', '15.9900'],
        'a decimal' => [fn () => Decimal::fromRawValue(159900, 4), '15.9900'],
        'a short string' => ['1.23', '1.2300'],
        'a negative string' => ['-1.23', '-1.2300'],
        'a negative fraction' => ['-0.5', '-0.5000'],
        'a string at the full scale' => ['1.9999', '1.9999'],
        'a string beyond the scale' => ['1.999999', '2.0000'],
        'a string with trailing zeros beyond the scale' => ['1.999900', '1.9999'],
        'an integer string' => ['100', '100.0000'],
        'a negative integer string' => ['-100', '-100.0000'],
        'an integer' => [100, '100.0000'],
        'a negative integer' => [-100, '-100.0000'],
        'a whole float' => [100.00, '100.0000'],
        'a negative whole float' => [-100.00, '-100.0000'],
    ]);

    it('rounds every kind of input to an integer when the scale is zero', function (float|int|string|Decimal $input) {
        $value = Decimal::create($input, 0);

        expect($value->asRawValue())->toEqual(16)
            ->and($value->asNumeric())->toEqual(16.0)
            ->and($value->asString())->toEqual('16');
    })->with([
        'a float' => [15.99],
        'a string' => ['15.99'],
        'a string with a decimal comma' => ['15,99'],
        'a decimal' => [fn () => Decimal::fromRawValue(159900, 4)],
    ]);

    it('rejects a negative scale', function (int|string $input) {
        expect(fn () => Decimal::create($input, -1))->toThrow(DomainException::class);
    })->with([
        'an integer' => [10000],
        'a string' => ['10.0'],
    ]);

    it('rejects a value that is neither a number nor a decimal', function ($value) {
        expect(fn () => Decimal::create($value))->toThrow(TypeError::class);
    })->with([
        'a date' => [fn () => new DateTime()],
        'true' => [true],
        'false' => [false],
    ]);

    it('creates zero', function () {
        $zero = Decimal::zero();

        expect($zero->asRawValue())->toEqual(0)
            ->and($zero->asNumeric())->toEqual(0)
            ->and($zero->asString())->toEqual('0.0000')
            ->and($zero->equals(Decimal::create(0)))->toBeTrue();
    });

    it('rounds half up by default when it scales down', function () {
        expect(Decimal::create('15.50', 0)->asRawValue())->toEqual(16);
    });

    it('rounds with the rounding mode it is given', function (int $roundingMode, int $expected) {
        expect(Decimal::create('15.50', 0, $roundingMode)->asRawValue())->toEqual($expected);
    })->with([
        'half up' => [PHP_ROUND_HALF_UP, 16],
        'half down' => [PHP_ROUND_HALF_DOWN, 15],
    ]);

    it('creates a value from a raw integer', function () {
        $simpleValue = Decimal::fromRawValue(100000, 4);
        $decimalValue = Decimal::fromRawValue(159900, 4);

        expect($simpleValue->asRawValue())->toEqual(100000)
            ->and($simpleValue->asNumeric())->toEqual(10)
            ->and($decimalValue->asRawValue())->toEqual(159900)
            ->and($decimalValue->asNumeric())->toEqual(15.99);
    });

    it('creates a value from a number', function () {
        $simpleValue = Decimal::fromNumeric(10, 4);
        $decimalValue = Decimal::fromNumeric(15.99, 4);

        expect($simpleValue->asRawValue())->toEqual(100000)
            ->and($simpleValue->asNumeric())->toEqual(10)
            ->and($decimalValue->asRawValue())->toEqual(159900)
            ->and($decimalValue->asNumeric())->toEqual(15.99);
    });

    it('rejects a string that is not numeric as a number', function () {
        expect(fn () => Decimal::fromNumeric('ABC'))->toThrow(InvalidArgumentException::class);
    });

    it('creates an equal value from a decimal of the same scale', function () {
        $value = Decimal::fromRawValue(100000, 4);

        expect(Decimal::fromDecimal($value, 4))->toEqual($value);
    });

    it('keeps the number when it creates a value from a decimal of another scale', function () {
        $value = Decimal::fromRawValue(100000, 4);

        expect(Decimal::fromDecimal($value, 8)->asNumeric())->toEqual($value->asNumeric());
    });
});

describe('formatting a value', function () {
    it('formats a value with its own scale unless it is given a number of digits', function () {
        $value = Decimal::create(10.0, 4);
        $otherScale = Decimal::create(15.99, 6);

        expect((string) $value)->toBe('10.0000')
            ->and($value->asString())->toBe('10.0000')
            ->and($value->asString(2))->toBe('10.00')
            ->and($value->asString(0))->toBe('10')
            ->and((string) $otherScale)->toBe('15.990000')
            ->and($otherScale->asString())->toBe('15.990000')
            ->and($otherScale->asString(2))->toBe('15.99')
            ->and($otherScale->asString(1))->toBe('15.9')
            ->and($otherScale->asString(0))->toBe('15');
    });

    it('formats a value without scale as the next integer', function () {
        $noScale = Decimal::create(15.99, 0);

        expect((string) $noScale)->toBe('16')
            ->and($noScale->asString())->toBe('16')
            ->and($noScale->asString(5))->toBe('16.00000')
            ->and($noScale->asString(2))->toBe('16.00')
            ->and($noScale->asString(1))->toBe('16.0')
            ->and($noScale->asString(0))->toBe('16');
    });
});

describe('changing the scale', function () {
    it('returns the same instance for the current scale', function () {
        $value = Decimal::create('10', 4);

        expect($value->withScale(4))->toBe($value);
    });

    it('keeps a whole number exact through every change of scale', function () {
        $value = Decimal::create('10', 4);

        expect($value->asRawValue())->toBe(100000)
            ->and($value->asNumeric())->toBe(10);

        $value = $value->withScale(6);

        expect($value->asRawValue())->toBe(10000000)
            ->and($value->asNumeric())->toBe(10);

        $value = $value->withScale(2);

        expect($value->asRawValue())->toBe(1000)
            ->and($value->asNumeric())->toBe(10);

        $value = $value->withScale(4);

        expect($value->asRawValue())->toBe(100000)
            ->and($value->asNumeric())->toBe(10);
    });

    it('loses precision when it scales below the digits of the value', function () {
        $value = Decimal::create('15.99', 4);

        expect($value->asRawValue())->toBe(159900)
            ->and($value->asNumeric())->toBe(15.99);

        $value = $value->withScale(6);

        expect($value->asRawValue())->toBe(15990000)
            ->and($value->asNumeric())->toBe(15.99);

        $value = $value->withScale(2);

        expect($value->asRawValue())->toBe(1599)
            ->and($value->asNumeric())->toBe(15.99);

        $value = $value->withScale(0);

        expect($value->asRawValue())->toBe(16)
            ->and($value->asNumeric())->toBe(16);

        $value = $value->withScale(4);

        expect($value->asRawValue())->toBe(160000)
            ->and($value->asNumeric())->toBe(16);
    });
});

describe('comparing two values', function () {
    it('tells whether two values are equal', function () {
        $a = Decimal::create(5);
        $b = Decimal::create(10);

        expect($a->equals($a))->toBeTrue()
            ->and($b->equals($b))->toBeTrue()
            ->and($a->equals(Decimal::create(5)))->toBeTrue()
            ->and($a->equals(Decimal::create(5, 8)))->toBeFalse()
            ->and($a->equals($b))->toBeFalse()
            ->and($b->equals($a))->toBeFalse();
    });

    it('tells whether two values are not equal', function () {
        $a = Decimal::create(5);
        $b = Decimal::create(10);

        expect($a->notEquals($a))->toBeFalse()
            ->and($b->notEquals($b))->toBeFalse()
            ->and($a->notEquals(Decimal::create(5)))->toBeFalse()
            ->and($a->notEquals(Decimal::create(5, 8)))->toBeTrue()
            ->and($a->notEquals($b))->toBeTrue()
            ->and($b->notEquals($a))->toBeTrue();
    });

    it('orders two values', function () {
        $a = Decimal::create(5);
        $b = Decimal::create(10);

        expect($a->compare($b))->toEqual(-1)
            ->and($b->compare($a))->toEqual(1)
            ->and($a->compare($a))->toEqual(0)
            ->and($b->compare($b))->toEqual(0);
    });

    it('tells whether a value is less than another', function () {
        $a = Decimal::create(5);
        $b = Decimal::create(10);

        expect($a->lessThan($b))->toBeTrue()
            ->and($a->lessThan($a))->toBeFalse()
            ->and($b->lessThan($a))->toBeFalse()
            ->and($b->lessThan($b))->toBeFalse()
            ->and($a->lessThanOrEqual($a))->toBeTrue()
            ->and($a->lessThanOrEqual($b))->toBeTrue()
            ->and($b->lessThanOrEqual($a))->toBeFalse()
            ->and($b->lessThanOrEqual($b))->toBeTrue();
    });

    it('tells whether a value is greater than another', function () {
        $a = Decimal::create(5);
        $b = Decimal::create(10);

        expect($a->greaterThan($a))->toBeFalse()
            ->and($a->greaterThan($b))->toBeFalse()
            ->and($b->greaterThan($a))->toBeTrue()
            ->and($b->greaterThan($b))->toBeFalse()
            ->and($a->greaterThanOrEqual($a))->toBeTrue()
            ->and($a->greaterThanOrEqual($b))->toBeFalse()
            ->and($b->greaterThanOrEqual($a))->toBeTrue()
            ->and($b->greaterThanOrEqual($b))->toBeTrue();
    });

    it('tells whether a value is positive', function () {
        expect(Decimal::create(10)->isPositive())->toBeTrue()
            ->and(Decimal::create(1)->isPositive())->toBeTrue()
            ->and(Decimal::create(0.1)->isPositive())->toBeTrue()
            ->and(Decimal::create(-0.1)->isPositive())->toBeFalse()
            ->and(Decimal::create(-1)->isPositive())->toBeFalse()
            ->and(Decimal::create(-10)->isPositive())->toBeFalse()
            ->and(Decimal::create(0)->isPositive())->toBeFalse()
            ->and(Decimal::create(0.00001, 4)->isPositive())->toBeFalse();
    });

    it('tells whether a value is negative', function () {
        expect(Decimal::create(10)->isNegative())->toBeFalse()
            ->and(Decimal::create(1)->isNegative())->toBeFalse()
            ->and(Decimal::create(0.1)->isNegative())->toBeFalse()
            ->and(Decimal::create(-0.1)->isNegative())->toBeTrue()
            ->and(Decimal::create(-1)->isNegative())->toBeTrue()
            ->and(Decimal::create(-10)->isNegative())->toBeTrue()
            ->and(Decimal::create(0)->isNegative())->toBeFalse()
            ->and(Decimal::create(0.00001, 4)->isNegative())->toBeFalse();
    });

    it('tells whether a value is zero', function () {
        expect(Decimal::create(0)->isZero())->toBeTrue()
            ->and(Decimal::create(0.0)->isZero())->toBeTrue()
            ->and(Decimal::create('0')->isZero())->toBeTrue()
            ->and(Decimal::create('0.00')->isZero())->toBeTrue()
            ->and(Decimal::fromRawValue(0)->isZero())->toBeTrue()
            ->and(Decimal::create(0.00001, 4)->isZero())->toBeTrue()
            ->and(Decimal::create(10)->isZero())->toBeFalse()
            ->and(Decimal::create(0.1)->isZero())->toBeFalse()
            ->and(Decimal::create(-0.1)->isZero())->toBeFalse()
            ->and(Decimal::create(-10)->isZero())->toBeFalse();
    });
});

describe('calculating with values', function () {
    it('returns a new instance with the result of an operation', function (int $input, int $expected, string $operation, array $arguments) {
        $value = Decimal::create($input);
        $result = $value->{$operation}(...$arguments);

        expect($result)->not->toBe($value)
            ->and($result->asNumeric())->toBe($expected);
    })->with([
        'withScale' => [100, 100, 'withScale', [2]],
        'abs' => [-10, 10, 'abs', []],
        'add' => [100, 110, 'add', [10]],
        'sub' => [100, 90, 'sub', [10]],
        'mul' => [100, 300, 'mul', [3]],
        'div' => [100, 50, 'div', [2]],
        'toAdditiveInverse' => [100, -100, 'toAdditiveInverse', []],
        'toPercentage' => [100, 50, 'toPercentage', [50]],
        'discount' => [100, 85, 'discount', [15]],
    ]);

    it('returns the same instance as the absolute value of a positive value', function () {
        $a = Decimal::create(5);
        $b = Decimal::create(-5);

        expect($a->abs())->toBe($a)
            ->and($a->equals($b))->toBeFalse()
            ->and($a->equals($b->abs()))->toBeTrue()
            ->and($b->abs()->asNumeric())->toEqual(5);
    });

    it('adds every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        expect(Decimal::create($a)->add($b)->asNumeric())->toEqual(30);
    })->with([
        'a float' => [15.50],
        'a string' => ['15.50'],
        'a decimal' => [fn () => Decimal::fromRawValue(155000)],
    ])->with([
        'a float' => [14.50],
        'a string' => ['14.50'],
        'a decimal' => [fn () => Decimal::fromRawValue(145000)],
    ]);

    it('subtracts every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        expect(Decimal::create($a)->sub($b)->asNumeric())->toEqual(1);
    })->with([
        'a float' => [15.50],
        'a string' => ['15.50'],
        'a decimal' => [fn () => Decimal::fromRawValue(155000)],
    ])->with([
        'a float' => [14.50],
        'a string' => ['14.50'],
        'a decimal' => [fn () => Decimal::fromRawValue(145000)],
    ]);

    it('multiplies by every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        expect(Decimal::create($a)->mul($b)->asNumeric())->toEqual(30);
    })->with([
        'an integer' => [15],
        'a float' => [15.00],
        'a decimal' => [fn () => Decimal::fromRawValue(150000)],
    ])->with([
        'an integer' => [2],
        'a float' => [2.00],
        'a decimal' => [fn () => Decimal::fromRawValue(20000)],
    ]);

    it('divides by every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        expect(Decimal::create($a)->div($b)->asNumeric())->toEqual(7.50);
    })->with([
        'an integer' => [15],
        'a float' => [15.00],
        'a decimal' => [fn () => Decimal::fromRawValue(150000)],
    ])->with([
        'an integer' => [2],
        'a float' => [2.00],
        'a decimal' => [fn () => Decimal::fromRawValue(20000)],
    ]);

    it('adds only values of the same scale', function () {
        $a = Decimal::create('10', 4);
        $b = Decimal::create('20', 8);
        $scaledB = $b->withScale(4);

        expect($scaledB->asNumeric())->toEqual($b->asNumeric())
            ->and($a->add($scaledB)->asNumeric())->toEqual(30)
            ->and(fn () => $a->add($b))->toThrow(DomainException::class);
    });

    it('subtracts only values of the same scale', function () {
        $a = Decimal::create('10', 4);
        $b = Decimal::create('20', 8);
        $scaledB = $b->withScale(4);

        expect($scaledB->asNumeric())->toEqual($b->asNumeric())
            ->and($a->sub($scaledB)->asNumeric())->toEqual(-10)
            ->and(fn () => $a->sub($b))->toThrow(DomainException::class);
    });

    it('refuses to divide by zero', function (float|int|string|Decimal $divisor) {
        $value = Decimal::fromRawValue(159900, 4);

        expect(fn () => $value->div(Decimal::create($divisor)))->toThrow(DivisionByZeroError::class);
    })->with([
        'an integer' => [0],
        'a float' => [0.0],
        'a string' => ['0'],
        'a decimal' => [fn () => Decimal::create(0, 4)],
    ]);

    it('divides by a divisor that the scale can still represent', function (float $divisor) {
        $value = Decimal::create('10', 4);

        expect(fn () => $value->div($divisor))->not->toThrow(DivisionByZeroError::class);
    })->with([
        'one tenth' => [0.1],
        'one hundredth' => [0.01],
        'one thousandth' => [0.001],
        'one ten-thousandth' => [0.0001],
    ]);

    it('refuses to divide by a divisor that the scale rounds to zero', function () {
        $value = Decimal::create('10', 4);

        expect(fn () => $value->div(0.00001))->toThrow(DivisionByZeroError::class);
    });

    it('turns a value into its additive inverse', function () {
        expect(Decimal::create('15.50')->toAdditiveInverse()->asString())->toBe('-15.5000')
            ->and(Decimal::create('-15.50')->toAdditiveInverse()->asString())->toBe('15.5000')
            ->and(Decimal::create(0)->toAdditiveInverse()->asNumeric())->toBe(0);
    });
});

describe('percentages', function () {
    it('takes a percentage of a value', function () {
        expect(Decimal::create(100)->toPercentage(80)->asNumeric())->toEqual(80)
            ->and(Decimal::create(100)->toPercentage(25)->asNumeric())->toEqual(25)
            ->and(Decimal::create(50)->toPercentage(50)->asNumeric())->toEqual(25)
            ->and(Decimal::create(100)->toPercentage(35)->asNumeric())->toEqual(35)
            ->and(Decimal::create(100)->toPercentage(200)->asNumeric())->toEqual(200);
    });

    it('discounts a value by a percentage', function () {
        expect(Decimal::create(100)->discount(15)->asNumeric())->toEqual(85)
            ->and(Decimal::create(100)->discount(50)->asNumeric())->toEqual(50)
            ->and(Decimal::create(100)->discount(30)->asNumeric())->toEqual(70);
    });

    it('tells which percentage one value is of another', function () {
        $origPrice = Decimal::create('129.99');
        $discountedPrice = Decimal::create('88.00');
        $a = Decimal::create(100);
        $b = Decimal::create(50);

        expect(round($discountedPrice->percentageOf($origPrice), 0))->toEqual(68)
            ->and(round($origPrice->percentageOf($discountedPrice), 0))->toEqual(148)
            ->and($a->percentageOf($a))->toEqual(100)
            ->and($b->percentageOf($b))->toEqual(100)
            ->and($a->percentageOf($b))->toEqual(200)
            ->and($b->percentageOf($a))->toEqual(50);
    });

    it('tells by which percentage one value is discounted from another', function () {
        $origPrice = Decimal::create('129.99');
        $discountedPrice = Decimal::create('88.00');
        $a = Decimal::create(100);
        $b = Decimal::create(50);
        $c = Decimal::create(30);

        expect(round($discountedPrice->discountPercentageOf($origPrice), 0))->toEqual(32)
            ->and($a->discountPercentageOf($a))->toEqual(0)
            ->and($b->discountPercentageOf($b))->toEqual(0)
            ->and($a->discountPercentageOf($b))->toEqual(-100)
            ->and($b->discountPercentageOf($a))->toEqual(50)
            ->and($c->discountPercentageOf($a))->toEqual(70)
            ->and($c->discountPercentageOf($b))->toEqual(40);
    });
});

describe('integer bounds', function () {
    it('throws an overflow exception above the greatest usable integer', function () {
        // The bound keeps a threshold of 1, so the greatest usable integer is PHP_INT_MAX - 1.
        $maxInt = Decimal::fromRawValue(1)->add(Decimal::fromRawValue(PHP_INT_MAX - 2));

        expect(fn () => $maxInt->add(Decimal::fromRawValue(1)))->toThrow(OverflowException::class);
    });

    it('throws an underflow exception below the smallest usable integer', function () {
        // The bound keeps a threshold of 1, so the smallest usable integer is -PHP_INT_MAX + 1.
        $minInt = Decimal::fromRawValue(-1)->add(Decimal::fromRawValue(~PHP_INT_MAX + 2));

        expect(fn () => $minInt->sub(Decimal::fromRawValue(1)))->toThrow(UnderflowException::class);
    });
});
