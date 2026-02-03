<?php
declare(strict_types=1);

/**
 * Inheritance: yes
 * Variants: no
 *
 * Fields Summary:
 */

namespace OpenDxp\Model\DataObject;

use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;
use OpenDxp\Model\DataObject\PreGetValueHookInterface;

/**
* @method static \OpenDxp\Model\DataObject\Customer\Listing getList(array $config = [])
*/

class Customer extends Concrete
{
protected $classId = "CU";
protected $className = "Customer";

public static function create(array $values = []): static
{
	$object = new static();
	$object->setValues($values);
	return $object;
}

}
