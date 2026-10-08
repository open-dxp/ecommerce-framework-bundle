<?php

declare(strict_types=1);

use OpenDxp\Bundle\EcommerceFrameworkBundle\DependencyInjection\Config\Processor\TenantProcessor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

it('leaves the config untouched without defaults', function () {
    $input = [
        'tenant1' => [
            'foo' => 'bar',
            'baz' => [
                'in',
                'ga',
            ],
        ],
    ];

    $merged = (new TenantProcessor())->mergeTenantConfig($input);

    expect($merged)->toEqual($input);
});

it('merges the defaults into every tenant and removes them', function () {
    $input = [
        '_defaults' => [
            'default' => 'value',
        ],
        'default' => [
            'foo' => 'bar',
        ],
        'tenant1' => [
            'baz' => [
                'in',
                'ga',
            ],
        ],
    ];

    $merged = (new TenantProcessor())->mergeTenantConfig($input);

    expect($merged)->toEqual([
        'default' => [
            'default' => 'value',
            'foo' => 'bar',
        ],
        'tenant1' => [
            'default' => 'value',
            'baz' => [
                'in',
                'ga',
            ],
        ],
    ]);
});

it('removes additional defaults for YAML inheritance without merging them', function () {
    $input = [
        '_defaults' => [
            'default' => 'value',
        ],
        '_defaults_foobar' => [
            'xy' => 'z',
        ],
        '_defaultsblahfoo' => [
            'blah' => 'foo',
        ],
        'tenant1' => [
            'foo' => 'bar',
        ],
        'tenant2' => [
            'baz' => [
                'in',
                'ga',
            ],
        ],
    ];

    $merged = (new TenantProcessor())->mergeTenantConfig($input);

    expect($merged)->toEqual([
        'tenant1' => [
            'default' => 'value',
            'foo' => 'bar',
        ],
        'tenant2' => [
            'default' => 'value',
            'baz' => [
                'in',
                'ga',
            ],
        ],
    ]);
});

it('extends associative arrays of the defaults', function () {
    $input = [
        '_defaults' => [
            'values' => [
                'A' => 'B',
                'C' => 'D',
            ],
        ],
        'tenant1' => [
            'values' => [
                'A' => 'B1',
            ],
        ],
        'tenant2' => [
            'values' => [
                'E' => 'F',
            ],
        ],
    ];

    $merged = (new TenantProcessor())->mergeTenantConfig($input);

    expect($merged)->toEqual([
        'tenant1' => [
            'values' => [
                'A' => 'B1',
                'C' => 'D',
            ],
        ],
        'tenant2' => [
            'values' => [
                'A' => 'B',
                'C' => 'D',
                'E' => 'F',
            ],
        ],
    ]);
});

it('appends the values of a tenant to sequential arrays of the defaults', function () {
    $input = [
        '_defaults' => [
            'values' => [
                'A',
                'B',
                'C',
            ],
        ],
        'tenant1' => [
            'values' => [
                'D',
                'E',
            ],
        ],
        'tenant2' => [
            'values' => ['F'],
        ],
    ];

    $merged = (new TenantProcessor())->mergeTenantConfig($input);

    expect($merged)->toEqual([
        'tenant1' => [
            'values' => [
                'A',
                'B',
                'C',
                'D',
                'E',
            ],
        ],
        'tenant2' => [
            'values' => [
                'A',
                'B',
                'C',
                'F',
            ],
        ],
    ]);
});

it('merges the defaults deeply', function () {
    $input = [
        '_defaults' => [
            'level1' => [
                'level11A' => [
                    'foo',
                    'bar',
                ],
                'level11B' => [
                    'x' => 'yz',
                    'y' => 'z',
                ],
            ],
        ],

        'tenant1' => [
            'level1' => [
                'level11B' => [
                    'y' => 'AA',
                ],
            ],
            'level2' => [
                'foo' => [
                    'bar',
                    'bazinga',
                ],
            ],
        ],

        'tenant2' => [
            'level1' => [
                'level11A' => [
                    'bazinga',
                ],
                'level11C' => [
                    'my' => 'custom element',
                ],
            ],
            'level2' => 'ABC',
        ],
    ];

    $merged = (new TenantProcessor())->mergeTenantConfig($input);

    expect($merged)->toEqual([
        'tenant1' => [
            'level1' => [
                'level11A' => [
                    'foo',
                    'bar',
                ],
                'level11B' => [
                    'x' => 'yz',
                    'y' => 'AA',
                ],
            ],
            'level2' => [
                'foo' => [
                    'bar',
                    'bazinga',
                ],
            ],
        ],

        'tenant2' => [
            'level1' => [
                'level11A' => [
                    'foo',
                    'bar',
                    'bazinga',
                ],
                'level11B' => [
                    'x' => 'yz',
                    'y' => 'z',
                ],
                'level11C' => [
                    'my' => 'custom element',
                ],
            ],
            'level2' => 'ABC',
        ],
    ]);
});

it('rejects a tenant value whose type does not match the default', function () {
    $input = [
        '_defaults' => [
            'values' => [
                'A',
                'B',
                'C',
            ],
        ],
        'tenant1' => [
            'values' => 'D;E',
        ],
    ];

    expect(fn () => (new TenantProcessor())->mergeTenantConfig($input))
        ->toThrow(InvalidConfigurationException::class);
});
