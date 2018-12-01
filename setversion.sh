#!/bin/bash
# version changing script
NAME=rapida
VERSION=0.0.19

FROM="$NAME v[0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2}"
TO="$NAME v$VERSION"

sed -ri "s#$FROM#$TO#" api/Config.php
sed -ri "s#$FROM#$TO#" README.md
echo $VERSION