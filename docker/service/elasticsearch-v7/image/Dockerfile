ARG image
FROM $image AS dev

RUN apt-get update && apt-get install -y curl && apt-get clean

HEALTHCHECK \
--start-period=60s \
--interval=2s \
--retries=3 \
--timeout=2000ms \
CMD /bin/sh -c \
"curl --fail -s http://localhost:9200/_cat/health | cut -d' ' -f4 | grep -qE 'yellow|green' || exit 1"
