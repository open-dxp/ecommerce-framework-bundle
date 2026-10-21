# Changelog

## 1.1.0
- [CHORE] [MIGRATION] The installer keeps its state in the settings store. Run the migrations after the update. They mark an existing
  installation as installed.
- [BUGFIX] The index configurations and workers, the cart manager, the order manager and the filter types are abstract
  services. The bundle derives a service per tenant from them. A container that makes its services public failed on
  them.
- [BUGFIX] The `locale` of the interpreter `ObjectValue` is optional, as its documentation says
- [CHORE] Replace Codeception with Pest and `open-dxp/test-foundation`
- [CHORE] Require `open-dxp/opendxp` ^1.5

## 1.0.2
- [ENHANCEMENT] Change to new css color variable prefix naming [#8](https://github.com/open-dxp/ecommerce-framework-bundle/pull/8)

## 1.0.1
- [CHORE] Update payment provider links and migration notes in documentation [#5](https://github.com/open-dxp/ecommerce-framework-bundle/pull/5)
- [CHORE] Update branding names [#6](https://github.com/open-dxp/ecommerce-framework-bundle/pull/6)
- [ENHANCEMENT] Add php 8.5 support [#7](https://github.com/open-dxp/ecommerce-framework-bundle/pull/7)

## 1.0.0
- [NEW FEATURE] Initial release
- [CHORE] Update test suite [#1](https://github.com/open-dxp/ecommerce-framework-bundle/pull/1)
- [CHORE] Update test files [#2](https://github.com/open-dxp/ecommerce-framework-bundle/pull/2)
- [CHORE] Rector level 0 [#3](https://github.com/open-dxp/ecommerce-framework-bundle/pull/3)
- [CHORE] Update naming references [#4](https://github.com/open-dxp/ecommerce-framework-bundle/pull/4)
