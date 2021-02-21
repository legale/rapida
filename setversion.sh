#!/bin/bash
# version changing script
NAME=rapida
VERSION=0.0.26

if [[ ! -z "$1" ]]; then
NEW_VERSION=$1
else
NEW_VERSION=$VERSION
fi



FROM="$NAME v[0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2}"
TO="$NAME v$VERSION"

sed -ri "s#$VERSION#$NEW_VERSION#" $0
sed -ri "s#$FROM#$TO#" api/Config.php
sed -ri "s#$FROM#$TO#" README.md
echo $NEW_VERSION
