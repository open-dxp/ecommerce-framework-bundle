<?php

declare(strict_types=1);

namespace OpenDxp\Bundle\EcommerceFrameworkBundle\Tests\Feature\Model;

use OpenDxp\Bundle\EcommerceFrameworkBundle\CoreExtensions\ObjectData\IndexFieldSelection;
use OpenDxp\Model\DataObject\Fieldcollection;
use OpenDxp\Model\DataObject\Fieldcollection\Data\FilterSelect;
use OpenDxp\Model\DataObject\Fieldcollection\Data\OrderByFields;
use OpenDxp\Model\DataObject\FilterDefinition;

beforeEach(function () {
    $this->definition = new FilterDefinition();
    $this->definition->setParentId(1);
    $this->definition->setKey(uniqid('filter_definition_'));
});

function reloaded(FilterDefinition $definition): FilterDefinition
{
    return FilterDefinition::getById($definition->getId(), ['force' => true]);
}

it('stores a list of index fields', function () {
    $this->definition->setOrderByAsc('carClass,color');

    $this->definition->save();

    expect(reloaded($this->definition)->getOrderByAsc())->toBe('carClass,color');
});

it('stores an index field chosen from a list', function () {
    $orderBy = new OrderByFields();
    $orderBy->setField('carClass');
    $this->definition->setDefaultOrderBy(new Fieldcollection([$orderBy]));

    $this->definition->save();

    expect(reloaded($this->definition)->getDefaultOrderBy()->get(0)->getField())->toBe('carClass');
});

it('stores an index field together with its tenant and its preselection', function () {
    $filter = new FilterSelect();
    $filter->setField(new IndexFieldSelection('default', 'carClass', 'red'));
    $this->definition->setFilters(new Fieldcollection([$filter]));

    $this->definition->save();

    $stored = reloaded($this->definition)->getFilters()->get(0)->getField();
    expect($stored)
        ->toBeInstanceOf(IndexFieldSelection::class)
        ->getTenant()
        ->toBe('default')
        ->getField()
        ->toBe('carClass')
        ->getPreSelect()
        ->toBe('red');
});
