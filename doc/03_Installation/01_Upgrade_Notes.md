# Upgrade Notes

### 1.1.0
* The installer keeps its state in the settings store. Run the migrations after the update. They mark an existing
  installation as installed.
* [BUGFIX] The index configurations and workers, the cart manager, the order manager and the filter types are abstract
  services. The bundle derives a service per tenant from them. A container that makes its services public failed on
  them.
* [CHORE] Replace Codeception with Pest and `open-dxp/test-foundation`
* [CHORE] Require `open-dxp/opendxp` ^1.5

### Migrating from `pimcore/ecommerce-framework-bundle` to `open-dxp/ecommerce-framework-bundle`
* Moved Bundle to `OpenDxp\Bundle\EcommerceFrameworkBundle\` namespace
* Renamed top-level config node to `opendxp_ecommerce_framework`
