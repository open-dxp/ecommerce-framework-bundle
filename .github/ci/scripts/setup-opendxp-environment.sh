#!/bin/bash

set -eu

mkdir -p var/config
mkdir -p bin

cp -r .github/ci/files/config/. config
cp -r .github/ci/files/templates/. templates
cp -r .github/ci/files/bin/console bin/console
cp -r .github/ci/files/public/. public
cp .github/ci/files/.env ./

chmod 755 bin/console

#
# Codeception Framework Stuff

touch ./.env

TEST_BUNDLE_TEST_DIR="$OPENDXP_PROJECT_ROOT/tests"
OPENDXP_CODECEPTION_FRAMEWORK="${OPENDXP_CODECEPTION_FRAMEWORK_PATH:-$OPENDXP_PROJECT_ROOT/opendxp-codeception-framework}"

cp "$OPENDXP_CODECEPTION_FRAMEWORK/src/Support/App/TestKernel.php" kernel/TestKernel.php

#
## Register test variables in .env
{
  echo "TEST_BUNDLE_NAME=OpenDxpEcommerceFrameworkBundle"
  echo "TEST_BUNDLE_NAMESPACE=OpenDxp\Bundle\EcommerceFrameworkBundle"
  echo "TEST_BUNDLE_INSTALLER_CLASS=OpenDxp\Bundle\EcommerceFrameworkBundle\Tools\Installer"

  echo "APP_ENV=test"
  echo "APP_DEBUG=true"
  echo "OPENDXP_KERNEL_CLASS=\App\TestKernel"
  echo "OPENDXP_CODECEPTION_FRAMEWORK=$OPENDXP_CODECEPTION_FRAMEWORK"
  echo "TEST_BUNDLE_TEST_DIR=$TEST_BUNDLE_TEST_DIR"
} >> ./.env
