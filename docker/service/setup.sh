#!/bin/sh

set -eu

export uid=$(id -u)
export gid=$(id -g)

echo "Running as: $uid:$gid"

# own all directories and files
sudo chown -R $uid:$gid ../../

# add execution for the current user and its group to init scripts
sudo chmod -v ug+x,o-wx \
../../app/default/.elasticsearch-http-requests/request.sh \
../../app/default/.elasticsearch-http-requests/init.sh

# Nginx: create dirs, set owner and group, set access permissions
mkdir -p \
./nginx-v1/image/files/var/log/nginx && \
\
sudo chown -R $uid:$gid \
./nginx-v1/image/files/var/log/nginx && \
\
sudo chmod -v -R ug=rwX,o=rX \
./nginx-v1/image/files/var/log/nginx && \

# Elasticsearch: create dirs, set owner and group, set access permissions
mkdir -p \
./elasticsearch-v7/image/files/usr/share/elasticsearch/config \
./elasticsearch-v7/image/files/usr/share/elasticsearch/data \
./elasticsearch-v7/image/files/usr/share/elasticsearch/logs \
./elasticsearch-v7/image/files/usr/share/elasticsearch/plugins && \
\
sudo chown -R $uid:0 \
./elasticsearch-v7/image/files/usr/share/elasticsearch/config \
./elasticsearch-v7/image/files/usr/share/elasticsearch/data \
./elasticsearch-v7/image/files/usr/share/elasticsearch/logs \
./elasticsearch-v7/image/files/usr/share/elasticsearch/plugins && \
\
sudo chmod -v -R ug=rwX,o=rX \
./elasticsearch-v7/image/files/usr/share/elasticsearch/config \
./elasticsearch-v7/image/files/usr/share/elasticsearch/data \
./elasticsearch-v7/image/files/usr/share/elasticsearch/logs \
./elasticsearch-v7/image/files/usr/share/elasticsearch/plugins

# compose up everyting
docker-compose --profile dev up --detach --build --force-recreate

# call composer install
docker-compose exec php-composer-v2 composer install

# call init for elasticsearch
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/init.sh"

echo "$ docker ps -a"
docker ps
echo ""

echo "$ docker stats --no-stream"
docker stats --no-stream
echo ""

echo "$ docker network ls --filter 'type=custom'"
docker network ls --filter 'type=custom'
echo ""

# inform about opening web browser
echo "Installation complete."
echo "Run web browser and go to:"
echo ""
echo "http://localhost:9090"
echo ""

echo "To see logs run:"
echo ""
echo "docker-compose logs"
echo ""

echo "To shut this application down, run:"
echo ""
echo "docker-compose down --remove-orphans"
echo ""

echo "See the How to play with it? section"
echo "to know other useful commands."
echo ""
