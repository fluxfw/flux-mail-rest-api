#!/usr/bin/env sh

set -e

bin="`dirname "$0"`"
root="$bin/.."

name="`basename "$(realpath "$root")"`"
image="$FLUX_PUBLISH_DOCKER_USER/$name"
tag="`get-release-tag "$root"`"

"$bin/build.sh"

tag-release "$root"
create-github-release "$root"

export DOCKER_CONFIG="$FLUX_PUBLISH_DOCKER_CONFIG_FOLDER"
docker push "$image:$tag"
docker push "$image:latest"
unset DOCKER_CONFIG

update-github-metadata "$root"
