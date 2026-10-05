# Upgrade Notes

### 1.1.0
* Requires OpenDXP 1.5.
* The installer keeps its state in the settings store. Run the migrations after the update. They mark an existing
  installation as installed.

### Migrating from `pimcore/ecommerce-framework-bundle` to `open-dxp/ecommerce-framework-bundle`
* Moved Bundle to `OpenDxp\Bundle\EcommerceFrameworkBundle\` namespace
* Renamed top-level config node to `opendxp_ecommerce_framework`
