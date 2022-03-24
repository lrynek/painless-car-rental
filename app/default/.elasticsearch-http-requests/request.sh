#!/bin/sh

# Example usage:
# ./request.sh ./Index/Create.http
# ./request.sh /app/default/.elasticsearch-http-requests/Index/Delete.http

set -eu

http_request_path="${1:-}"

[ -n "$http_request_path" ] || \
    { printf "Usage: $1 <path to request file>\n" && exit 1; }

[ -n "${DOCKER_ELASTICSEARCH_HOST:-}" ] || \
    { printf "Set ENV varialbe DOCKER_ELASTICSEARCH_HOST first\n" && exit 1 ;}

RED='\033[31;5;7m'
GREEN='\033[1;0;42m'
NC='\033[0m' # No Color

SCRIPTPATH="$( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )";

# if request path is not absolute make it relative to the script
[ ! "$http_request_path" = "${http_request_path#/}" ] || \
    http_request_path="$SCRIPTPATH/$http_request_path"

[ -f $http_request_path ] || \
    { printf "Path does not point to a file, path: $1\n" && exit 1; }

[ -r "$http_request_path" ] || \
    { printf "File is not readable, check permissions, path: $1\n" && exit 1; }

# read content of a file
content="$(cat "$http_request_path" || { 1>&2 printf "Could not read the file: $http_request_path\n" && exit 1; })"

# dissasemble request to its part
request="$(printf "$content" | head -n1)"
method="$(printf "$request" | grep -oE '^\w+')"
url=$(printf "$request" | sed 's/^\w* //')
header="$(printf "$content\n" | awk '/http/,/^$/' | tail -n+2 | sed '$d')"
body="$(printf "$content" | tail -n+"$(printf "$content\n" | awk '/http/,/^$/' | wc -l)" | tail -n+2)"

# convert localhost to elasticsearch service host
find='http://localhost:9200'
replace="$DOCKER_ELASTICSEARCH_HOST"
url="$(printf "$url\n" | awk -v r="$replace" -v f="$find" '{ gsub(f,r); print $0 }')"

# create header options for curl
curl_header=''
newline="$(printf "\nx")" && newline="${newline%x}"
IFS="$newline"
for head in $header; do 
    curl_header="${curl_header} -H '$head'"
done

option_data=''
[ -z "$body" ] || option_data=" --data-raw \\${newline}'$body${newline}'"

# escape body's single quote '
body="$(printf "$body" | sed "s/'/\\\'/g")"

from_newline=''
[ -z "$option_data" ] || from_newline="\\${newline}"

#Bugfix for: elasticsearch 7 deprec. use of _doc: '{"error":{"root_cause":[{"type":"illegal_argument_exception","reason":"The mapping definition cannot be nested under a type [_doc] unless include_type_name is set to true."}],"type":"illegal_argument_exception","reason":"The mapping definition cannot be nested under a type [_doc] unless include_type_name is set to true."},"status":400}'
[ ! "$method" = 'PUT' ] || url="${url}?include_type_name=true"

command_request="curl -sSL -X ${method:-GET}${curl_header}${option_data} ${from_newline}'$url'"
printf "Performing cURL request:\n${command_request}\n"

# example success responses
# expected_pattern='\{"count":0,"_shards":{"total":1,"successful":1,"skipped":0,"failed":0}}'
# expected_pattern='^\{"acknowledged":true' # Delete,

# check for error response
expected_pattern='^\s*\{\s*"error"' # Delete,
response="$(printf "$command_request\n" | sh || ec=$? )" 

# validate response
printf "$response" | grep -vE "$expected_pattern" > /dev/null || \
    { printf "${RED}FAIL${NC}\nThere was an error with cURL exit code: ${ec:-0}, and response: $response\n" && exit ${ec:-1}; }

printf "$response\n"
printf "${GREEN}Success.${NC}\n"
