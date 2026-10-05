<?php

declare(strict_types=1);

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

it('stores a list of index fields', function () {
    $this->definition->setOrderByAsc('carClass,color');
    $this->definition->save();

    expect(FilterDefinition::getById($this->definition->getId(), ['force' => true])->getOrderByAsc())->toBe('carClass,color');
});

it('stores an index field chosen from a list', function () {
    $orderBy = new OrderByFields();
    $orderBy->setField('carClass');
    $this->definition->setDefaultOrderBy(new Fieldcollection([$orderBy]));
    $this->definition->save();

    $stored = FilterDefinition::getById($this->definition->getId(), ['force' => true])->getDefaultOrderBy()->get(0);

    expect($stored->getField())->toBe('carClass');
});

it('stores an index field together with its tenant and its preselection', function () {
    $filter = new FilterSelect();
    $filter->setField(new IndexFieldSelection('default', 'carClass', 'red'));
    $this->definition->setFilters(new Fieldcollection([$filter]));
    $this->definition->save();

    $stored = FilterDefinition::getById($this->definition->getId(), ['force' => true])->getFilters()->get(0)->getField();

    expect($stored)->toBeInstanceOf(IndexFieldSelection::class)
        ->and($stored->getTenant())->toBe('default')
        ->and($stored->getField())->toBe('carClass')
        ->and($stored->getPreSelect())->toBe('red');
});
