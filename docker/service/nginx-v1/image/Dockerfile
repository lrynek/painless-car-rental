ARG image
FROM $image AS dev

RUN apt-get update && apt-get install -y curl && apt-get clean

# Note that there must be an extra 'health check endpoint' defined for the healthcheck in ./files/etc/nginx/conf.d/default.conf
# or you need to handle that with your PHP application but if you want just to return http response
# then Nginx config file is ideal for that so the health checks don't bother your PHP application.
#
HEALTHCHECK \
--start-period=30s \
--interval=2s \
--retries=3 \
--timeout=2000ms \
CMD /bin/sh -c \
"curl --fail -s http://localhost:80/_cat/health | grep -qE 'green' || exit 1"
