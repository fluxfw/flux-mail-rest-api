#!/usr/bin/env sh

set -e

bin="`dirname "$0"`"
root="$bin/.."

update-release-version "$root"

"$bin/build.sh"
