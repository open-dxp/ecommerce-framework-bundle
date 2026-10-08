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

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Unit\Type;

use Closure;
use DateTime;
use DivisionByZeroError;
use DomainException;
use InvalidArgumentException;
use OpenDxp\Bundle\EcommerceFrameworkBundle\Type\Decimal;
use OverflowException;
use TypeError;
use UnderflowException;

describe('creating a value', function () {
    it('represents a value as a raw integer, a number and a string', function () {
        $value = Decimal::create(10.0, 4);

        expect($value)
            ->asRawValue()
            ->toEqual(100000)
            ->asNumeric()
            ->toEqual(10.0)
            ->asString()
            ->toBe('10.0000');
    });

    it('creates a value with a scale of four from every kind of input', function (
        float|int|string|Decimal $input,
        string $expected,
    ) {
        $value = Decimal::create($input);

        expect($value->asString())->toBe($expected);
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

        expect($value)
            ->asRawValue()
            ->toEqual(16)
            ->asNumeric()
            ->toEqual(16.0)
            ->asString()
            ->toEqual('16');
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

    it('rejects a value that is neither a number nor a decimal', function (mixed $value) {
        expect(fn () => Decimal::create($value))->toThrow(TypeError::class);
    })->with([
        'a date' => [fn () => new DateTime()],
        'true' => [true],
        'false' => [false],
    ]);

    it('creates zero', function () {
        $zero = Decimal::zero();

        expect($zero)
            ->asRawValue()
            ->toEqual(0)
            ->asNumeric()
            ->toEqual(0)
            ->asString()
            ->toEqual('0.0000')
            ->and($zero->equals(Decimal::create(0)))
            ->toBeTrue();
    });

    it('rounds half up by default when it scales down', function () {
        $value = Decimal::create('15.50', 0);

        expect($value->asRawValue())->toEqual(16);
    });

    it('rounds with the rounding mode it is given', function (int $roundingMode, int $expected) {
        $value = Decimal::create('15.50', 0, $roundingMode);

        expect($value->asRawValue())->toEqual($expected);
    })->with([
        'half up' => [PHP_ROUND_HALF_UP, 16],
        'half down' => [PHP_ROUND_HALF_DOWN, 15],
    ]);

    it('creates a value from a raw integer', function (int $raw, float|int $numeric) {
        $value = Decimal::fromRawValue($raw, 4);

        expect($value->asNumeric())->toEqual($numeric);
    })->with([
        'a whole number' => [100000, 10],
        'a fraction' => [159900, 15.99],
    ]);

    it('creates a value from a number', function (float|int $numeric, int $raw) {
        $value = Decimal::fromNumeric($numeric, 4);

        expect($value->asRawValue())->toEqual($raw);
    })->with([
        'a whole number' => [10, 100000],
        'a fraction' => [15.99, 159900],
    ]);

    it('rejects a string that is not numeric as a number', function () {
        Decimal::fromNumeric('ABC');
    })->throws(InvalidArgumentException::class);

    it('creates an equal value from a decimal of the same scale', function () {
        $value = Decimal::fromRawValue(100000, 4);

        $copy = Decimal::fromDecimal($value, 4);

        expect($copy)->toEqual($value);
    });

    it('keeps the number when it creates a value from a decimal of another scale', function () {
        $value = Decimal::fromRawValue(100000, 4);

        $copy = Decimal::fromDecimal($value, 8);

        expect($copy->asNumeric())->toEqual($value->asNumeric());
    });
});

describe('formatting a value', function () {
    it('formats a value with its own scale', function (int $scale, string $expected) {
        $value = Decimal::create(15.99, $scale);

        expect((string) $value)->toBe($expected);
    })->with([
        'a scale of four' => [4, '15.9900'],
        'a scale of six' => [6, '15.990000'],
        'a scale of zero' => [0, '16'],
    ]);

    it('formats a value with the number of digits it is given', function (int $scale, int $digits, string $expected) {
        $value = Decimal::create(15.99, $scale);

        expect($value->asString($digits))->toBe($expected);
    })->with([
        'two digits of a scale of four' => [4, 2, '15.99'],
        'no digits of a scale of four' => [4, 0, '15'],
        'one digit of a scale of six' => [6, 1, '15.9'],
        'five digits of a scale of zero' => [0, 5, '16.00000'],
        'two digits of a scale of zero' => [0, 2, '16.00'],
    ]);

    it('formats a value as a string the same way it casts it', function () {
        $value = Decimal::create(10.0, 4);

        expect((string) $value)->toBe($value->asString());
    });
});

describe('changing the scale', function () {
    it('returns the same instance for the current scale', function () {
        $value = Decimal::create('10', 4);

        expect($value->withScale(4))->toBe($value);
    });

    it('keeps a whole number exact through every change of scale', function (array $scales, int $raw) {
        $value = array_reduce(
            $scales,
            static fn (Decimal $value, int $scale): Decimal => $value->withScale($scale),
            Decimal::create('10', 4),
        );

        expect($value)
            ->asRawValue()
            ->toBe($raw)
            ->asNumeric()
            ->toBe(10);
    })->with([
        'up to six' => [[6], 10000000],
        'down to two' => [[6, 2], 1000],
        'back to four' => [[6, 2, 4], 100000],
    ]);

    it('loses precision when it scales below the digits of the value', function (
        array $scales,
        int $raw,
        float|int $numeric,
    ) {
        $value = array_reduce(
            $scales,
            static fn (Decimal $value, int $scale): Decimal => $value->withScale($scale),
            Decimal::create('15.99', 4),
        );

        expect($value)
            ->asRawValue()
            ->toBe($raw)
            ->asNumeric()
            ->toBe($numeric);
    })->with([
        'up to six' => [[6], 15990000, 15.99],
        'down to two' => [[6, 2], 1599, 15.99],
        'down to zero' => [[6, 2, 0], 16, 16],
        'back to four after zero' => [[6, 2, 0, 4], 160000, 16],
    ]);
});

describe('comparing two values', function () {
    dataset('pairs of values', [
        'five and five' => [fn () => Decimal::create(5), fn () => Decimal::create(5), true],
        'five and five of another scale' => [fn () => Decimal::create(5), fn () => Decimal::create(5, 8), false],
        'five and ten' => [fn () => Decimal::create(5), fn () => Decimal::create(10), false],
        'ten and five' => [fn () => Decimal::create(10), fn () => Decimal::create(5), false],
    ]);

    it('tells whether two values are equal', function (Decimal $a, Decimal $b, bool $equal) {
        expect($a->equals($b))->toBe($equal);
    })->with('pairs of values');

    it('tells whether two values are not equal', function (Decimal $a, Decimal $b, bool $equal) {
        expect($a->notEquals($b))->toBe(!$equal);
    })->with('pairs of values');

    it('orders two values', function (int $a, int $b, int $order) {
        $first = Decimal::create($a);

        expect($first->compare(Decimal::create($b)))->toEqual($order);
    })->with([
        'five and ten' => [5, 10, -1],
        'ten and five' => [10, 5, 1],
        'five and five' => [5, 5, 0],
    ]);

    it('tells whether a value is less than another', function (int $a, int $b, bool $less, bool $lessOrEqual) {
        $first = Decimal::create($a);
        $second = Decimal::create($b);

        expect($first->lessThan($second))
            ->toBe($less)
            ->and($first->lessThanOrEqual($second))
            ->toBe($lessOrEqual);
    })->with([
        'five and ten' => [5, 10, true, true],
        'five and five' => [5, 5, false, true],
        'ten and five' => [10, 5, false, false],
    ]);

    it('tells whether a value is greater than another', function (int $a, int $b, bool $greater, bool $greaterOrEqual) {
        $first = Decimal::create($a);
        $second = Decimal::create($b);

        expect($first->greaterThan($second))
            ->toBe($greater)
            ->and($first->greaterThanOrEqual($second))
            ->toBe($greaterOrEqual);
    })->with([
        'ten and five' => [10, 5, true, true],
        'five and five' => [5, 5, false, true],
        'five and ten' => [5, 10, false, false],
    ]);

    dataset('signs of values', [
        'ten' => [10, true, false],
        'one' => [1, true, false],
        'one tenth' => [0.1, true, false],
        'minus one tenth' => [-0.1, false, true],
        'minus one' => [-1, false, true],
        'minus ten' => [-10, false, true],
        'zero' => [0, false, false],
        'a fraction below the scale' => [0.00001, false, false],
    ]);

    it('tells whether a value is positive', function (float|int $input, bool $positive, bool $negative) {
        $value = Decimal::create($input, 4);

        expect($value->isPositive())->toBe($positive);
    })->with('signs of values');

    it('tells whether a value is negative', function (float|int $input, bool $positive, bool $negative) {
        $value = Decimal::create($input, 4);

        expect($value->isNegative())->toBe($negative);
    })->with('signs of values');

    it('tells whether a value is zero', function (float|int|string|Decimal $input, bool $zero) {
        $value = Decimal::create($input);

        expect($value->isZero())->toBe($zero);
    })->with([
        'the integer zero' => [0, true],
        'the float zero' => [0.0, true],
        'the string zero' => ['0', true],
        'the string zero with decimals' => ['0.00', true],
        'the raw value zero' => [fn () => Decimal::fromRawValue(0), true],
        'a fraction below the scale' => [0.00001, true],
        'ten' => [10, false],
        'one tenth' => [0.1, false],
        'minus one tenth' => [-0.1, false],
        'minus ten' => [-10, false],
    ]);
});

describe('calculating with values', function () {
    it('returns a new instance with the result of an operation', function (
        int $input,
        Closure $operation,
        int $expected,
    ) {
        $value = Decimal::create($input);

        $result = $operation($value);

        expect($result)
            ->not->toBe($value)
            ->asNumeric()
            ->toBe($expected);
    })->with([
        'a change of scale' => [100, fn (Decimal $value): Decimal => $value->withScale(2), 100],
        'the absolute value of a negative value' => [-10, fn (Decimal $value): Decimal => $value->abs(), 10],
        'an addition' => [100, fn (Decimal $value): Decimal => $value->add(10), 110],
        'a subtraction' => [100, fn (Decimal $value): Decimal => $value->sub(10), 90],
        'a multiplication' => [100, fn (Decimal $value): Decimal => $value->mul(3), 300],
        'a division' => [100, fn (Decimal $value): Decimal => $value->div(2), 50],
        'the additive inverse' => [100, fn (Decimal $value): Decimal => $value->toAdditiveInverse(), -100],
        'a percentage' => [100, fn (Decimal $value): Decimal => $value->toPercentage(50), 50],
        'a discount' => [100, fn (Decimal $value): Decimal => $value->discount(15), 85],
    ]);

    it('returns the same instance as the absolute value of a positive value', function () {
        $value = Decimal::create(5);

        expect($value->abs())->toBe($value);
    });

    it('turns a negative value into its absolute value', function () {
        $value = Decimal::create(-5);

        $absolute = $value->abs();

        expect($absolute->equals(Decimal::create(5)))->toBeTrue();
    });

    it('adds every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        $sum = Decimal::create($a)->add($b);

        expect($sum->asNumeric())->toEqual(30);
    })->with([
        'a float' => [15.50],
        'a string' => ['15.50'],
        'a decimal' => [fn () => Decimal::fromRawValue(155000)],
    ])->with([
        'to a float' => [14.50],
        'to a string' => ['14.50'],
        'to a decimal' => [fn () => Decimal::fromRawValue(145000)],
    ]);

    it('subtracts every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        $difference = Decimal::create($a)->sub($b);

        expect($difference->asNumeric())->toEqual(1);
    })->with([
        'a float' => [15.50],
        'a string' => ['15.50'],
        'a decimal' => [fn () => Decimal::fromRawValue(155000)],
    ])->with([
        'from a float' => [14.50],
        'from a string' => ['14.50'],
        'from a decimal' => [fn () => Decimal::fromRawValue(145000)],
    ]);

    it('multiplies by every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        $product = Decimal::create($a)->mul($b);

        expect($product->asNumeric())->toEqual(30);
    })->with([
        'an integer' => [15],
        'a float' => [15.00],
        'a decimal' => [fn () => Decimal::fromRawValue(150000)],
    ])->with([
        'by an integer' => [2],
        'by a float' => [2.00],
        'by a decimal' => [fn () => Decimal::fromRawValue(20000)],
    ]);

    it('divides by every kind of operand', function (float|int|string|Decimal $a, float|int|string|Decimal $b) {
        $quotient = Decimal::create($a)->div($b);

        expect($quotient->asNumeric())->toEqual(7.50);
    })->with([
        'an integer' => [15],
        'a float' => [15.00],
        'a decimal' => [fn () => Decimal::fromRawValue(150000)],
    ])->with([
        'by an integer' => [2],
        'by a float' => [2.00],
        'by a decimal' => [fn () => Decimal::fromRawValue(20000)],
    ]);

    it('adds a value after it took the same scale', function () {
        $value = Decimal::create('10', 4);
        $other = Decimal::create('20', 8)->withScale(4);

        $sum = $value->add($other);

        expect($sum->asNumeric())->toEqual(30);
    });

    it('adds no value of another scale', function () {
        $value = Decimal::create('10', 4);

        expect(fn () => $value->add(Decimal::create('20', 8)))->toThrow(DomainException::class);
    });

    it('subtracts a value after it took the same scale', function () {
        $value = Decimal::create('10', 4);
        $other = Decimal::create('20', 8)->withScale(4);

        $difference = $value->sub($other);

        expect($difference->asNumeric())->toEqual(-10);
    });

    it('subtracts no value of another scale', function () {
        $value = Decimal::create('10', 4);

        expect(fn () => $value->sub(Decimal::create('20', 8)))->toThrow(DomainException::class);
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

    it('turns a value into its additive inverse', function (string $input, string $expected) {
        $inverse = Decimal::create($input)->toAdditiveInverse();

        expect($inverse->asString())->toBe($expected);
    })->with([
        'a positive value' => ['15.50', '-15.5000'],
        'a negative value' => ['-15.50', '15.5000'],
        'zero' => ['0', '0.0000'],
    ]);
});

describe('percentages', function () {
    it('takes a percentage of a value', function (int $value, int $percent, int $expected) {
        $part = Decimal::create($value)->toPercentage($percent);

        expect($part->asNumeric())->toEqual($expected);
    })->with([
        '80 percent of 100' => [100, 80, 80],
        '25 percent of 100' => [100, 25, 25],
        '50 percent of 50' => [50, 50, 25],
        '200 percent of 100' => [100, 200, 200],
    ]);

    it('discounts a value by a percentage', function (int $percent, int $expected) {
        $discounted = Decimal::create(100)->discount($percent);

        expect($discounted->asNumeric())->toEqual($expected);
    })->with([
        '15 percent' => [15, 85],
        '50 percent' => [50, 50],
        '30 percent' => [30, 70],
    ]);

    it('tells which percentage one value is of another', function (string $value, string $base, int $expected) {
        $percentage = Decimal::create($value)->percentageOf(Decimal::create($base));

        expect(round($percentage))->toEqual($expected);
    })->with([
        'a discounted price of the original one' => ['88.00', '129.99', 68],
        'an original price of the discounted one' => ['129.99', '88.00', 148],
        'a value of itself' => ['100', '100', 100],
        'a value of its half' => ['100', '50', 200],
        'a value of its double' => ['50', '100', 50],
    ]);

    it('tells by which percentage one value is discounted from another', function (
        string $value,
        string $base,
        int $expected,
    ) {
        $percentage = Decimal::create($value)->discountPercentageOf(Decimal::create($base));

        expect(round($percentage))->toEqual($expected);
    })->with([
        'a discounted price from the original one' => ['88.00', '129.99', 32],
        'a value from itself' => ['100', '100', 0],
        'a value from its half' => ['100', '50', -100],
        'a value from its double' => ['50', '100', 50],
        'thirty from a hundred' => ['30', '100', 70],
        'thirty from fifty' => ['30', '50', 40],
    ]);
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
