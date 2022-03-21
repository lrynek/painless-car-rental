#!/bin/sh

set -eu

SCRIPTPATH="$( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )";

"$SCRIPTPATH/request.sh" "$SCRIPTPATH/Index/Delete.http" || true
"$SCRIPTPATH/request.sh" "$SCRIPTPATH/Index/Create.http" && \
"$SCRIPTPATH/request.sh" "$SCRIPTPATH/Index/Bulk.http" && sleep 1 && \
"$SCRIPTPATH/request.sh" "$SCRIPTPATH/Index/Count.http"

printf "Done!\n"