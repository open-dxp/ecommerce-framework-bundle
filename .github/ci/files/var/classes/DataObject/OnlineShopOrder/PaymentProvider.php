<?php
declare(strict_types=1);

namespace OpenDxp\Model\DataObject\OnlineShopOrder;

use OpenDxp\Model\DataObject\Exception\InheritanceParentNotFoundException;

class PaymentProvider extends \OpenDxp\Model\DataObject\Objectbrick {

protected $brickGetters = ['PaymentProviderUnzer'];


protected ?\OpenDxp\Model\DataObject\Objectbrick\Data\PaymentProviderUnzer $PaymentProviderUnzer = null;

    public function getPaymentProviderUnzer(): ?\OpenDxp\Model\DataObject\Objectbrick\Data\PaymentProviderUnzer
{
	return $this->PaymentProviderUnzer;
}

    public function setPaymentProviderUnzer(\OpenDxp\Model\DataObject\Objectbrick\Data\PaymentProviderUnzer $PaymentProviderUnzer): static
{
	$this->PaymentProviderUnzer = $PaymentProviderUnzer;
	return $this;
}

}

