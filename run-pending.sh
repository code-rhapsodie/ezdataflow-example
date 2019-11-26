#!/bin/bash

cd "$(dirname "$0")"
export SYMFONY_ENV=prod

/usr/bin/php -d memory_limit=2G bin/console code-rhapsodie:dataflow:run-pending
