#!/bin/sh
sed -i 's/\"kaltura\/kms-ci-framework": "dev-master"/"kaltura\/kms-ci-framework": "dev-master#'$1'"/' composer.json
