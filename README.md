# Painless Car Rental
### Painless / Painful? Own ranking system in PHP/Elasticsearch
![image](https://user-images.githubusercontent.com/36886649/145262691-59852b28-141a-4d1a-ac9c-73bd1e5b00bf.png)

## Purpose and constraints
This is the simple educational project prepared to support my presentation during [**PHPers Summit 2021**](https://2021.summit.phpers.pl/pl/) conference (more recently on [**Warszawskie Dni Informatyki 2022**](https://warszawskiedniinformatyki.pl/en/) and [**4Developers 2022**](https://4developers.org.pl/bio_online_2022/#id=48004)) and to allow participants to play with Elasticsearch scoring. It is not intended to expose any architectural patterns of the code itself, so please don't stick to the directory structure or the overall code architecture too much 😉.

| [Docplanner Tech](https://docplanner.tech) | [PHPers Summit 2021](https://2021.summit.phpers.pl/pl/) | [Warszawskie Dni Informatyki](https://warszawskiedniinformatyki.pl/en/) | [**4Developers**](https://4developers.org.pl/)
| :---:         |     :---:      |     :---:      |     :---:      |
| ![image](https://user-images.githubusercontent.com/36886649/135843518-9d4b2ec1-32dc-4226-a63c-b173d9b0706e.png) | ![image](https://user-images.githubusercontent.com/36886649/135534953-338af09d-d2c6-43ee-9407-137253cc4e13.png) | <img width="333" alt="image" src="https://user-images.githubusercontent.com/36886649/159000173-5560813a-2a13-452d-8444-c17d4405140f.png"> | <img width="249" alt="image" src="https://user-images.githubusercontent.com/36886649/159000375-33ef5c35-e39f-4115-a39c-b25674d4655d.png">

### Table of contents

- [Requirements](#Requirements)
- [Docker and Docker Compose upgrade](#Docker-and-Docker-Compose-upgrade)
- [Project setup](#Project-setup)
  - [Quick setup (without Docker cleanup)](#quick-setup-without-docker-cleanup)
  - [Step by step setup](#Step-by-step-setup)
    - [Clone repository and create directories](#Clone-repository-and-create-directories)
    - [Introduction to docker and docker-compose](#Introduction-to-docker-and-docker-compose)
    - [Docker cleanup (optional)](#docker-cleanup-optional)
      - [Containers cleanup](#Containers-cleanup)
      - [Networks cleanup](#Networks-cleanup)
      - [Volumes cleanup](#Volumes-cleanup)
      - [Development time cleanup](#Development-time-cleanup)
    - [Docker compose project setup](#Docker-compose-project-setup)
      - [Start services](#Start-services)
      - [Install composer dependencies](#Install-composer-dependencies)
    - [Elasticsearch index creation and population](#Elasticsearch-index-creation-and-population)
      - [Make scripts executable](#Make-scripts-executable)
      - [Quick setup with init.sh](#quick-setup-with-initsh)
      - [Step by step setup with request.sh](#Step-by-step-setup-with-request.sh)
        - [Create index](#Create-index)
        - [Populate index with data](#Populate-index-with-data)
        - [Get count of items](#Get-count-of-items)
        - [Search](#Search)
        - [Delete index](#Delete-index)
      - [Step by step other methods](#Step-by-step-other-methods)
- [How to play with it?](#how-to-play-with-it)
  - [Docker](#Docker)
    - [Run all services](#Run-all-services)
    - [Release the shell lock](#Release-the-shell-lock)
    - [Run all services without shell locking](#Run-all-services-without-shell-locking)
    - [Run a specific profile](#Run-a-specific-profile)
    - [Stop services](#Stop-services)
    - [Remove service containers](#Remove-service-containers)
    - [Stop & remove service containers with their network](#stop--remove-service-containers-with-their-network)
    - [Run, Stop, Remove](#run-stop-remove)
    - [Build images](#Build-images)
    - [Composer](#Composer)
    - [Monitoring](#Monitoring)
    - [Reinstallation](#Reinstallation)
  - [Elasticsearch](#Elasticsearch)
    - [Reinitialize](#Reinitialize)
    - [Elasticsearch code](#Elasticsearch-code)
- [Uninstallation](#Uninstallation)
- [Credits](#Credits)
- [Copyrights](#Copyrights)

 [^TOC^](#Table-of-contents)

## Requirements

- docker (tested on v20.10.13)
- docker-compose (tested on v2.3.3)
- linux (tested on Ubuntu 18.04)

 [^TOC^](#Table-of-contents)

## Docker and Docker Compose upgrade

If you use the `docker` and/or `docker-compose` provided by your system vendor - you installed it on Ubuntu  for eg. by `apt-get install docker` or `apt-get install docker-compose` then probably at least your installation of the `docker-compose`  has to low version to run this application. 

You may need to also to update the version of the `docker`  but for the Ubuntu it can be done by its GUI [Software Updater](https://en.wikipedia.org/wiki/Ubuntu_Software_Updater).



You may check your current version of the `docker-compose` by running:

```sh
docker-compose --version
```

If it is `1.x` then you need to upgrade it.



**Ubuntu `docker-compose` most recent version installation**:

Get info about available version to download:

```sh
VERSION="$(curl --silent 'https://api.github.com/repos/docker/compose/releases/latest' | grep -Po '"tag_name": "\K.*\d')" && echo "Available version: $VERSION"
```

example terminal output:

```sh
Available version: v2.3.3
```

if you don't see any version here discontinue and do not follow other steps but try on your own to upgrade the `docker-compose`.



Check if you have `docker-compose` installed and where, run:

```sh
DESTINATION="$(find / -iname 'docker-compose' -type f -executable 2> /dev/null || true)" && 
{ [ -z ${DESTINATION:-} ] && echo "DESTINATION must be set manually"; } || echo "DESTINATION is set to: $DESTINATION"
```

example terminal output:

```
/usr/bin/docker-compose
```

or "DESTINATION must be set manually" if you haven't installed `docker-compose`, then variable `DESTINATION` must be later set by you manually (continue with the steps as below).



Uninstall current version only if DESTINATION is set:

```sh
if [ -z "${DESTINATION:-}" ]; then
    echo "No need for uninstalling, docker-compose not found"
else
    echo "Uninstalling docker-compose"
    sudo apt-get remove docker-compose
fi
```



in case you have seen:

> DESTINATION must be set manually

now is the time to set the destination to the path you wish, for example run:

```sh
DESTINATION=/usr/bin/docker-compose
```



Download the `docker-compose` file and make it executable:

```sh
echo "Downloading file to: ${DESTINATION:?}"
sudo curl -L "https://github.com/docker/compose/releases/download/${VERSION:?}/docker-compose-$(uname -s)-$(uname -m)" -o "${DESTINATION:?}"

echo "Changing permissions to rx"
sudo chmod a=rx "${DESTINATION:?}"
```



Now you should be able to run `docker-compose` and check its new version, run:

```
docker-compose --version
```

example terminal output:

```
Docker Compose version v2.3.3
```



If by this time you don't have installed new version of the `docker-compose` then you may install again the previous version of the `docker-compose` by running:

```sh
sudo apt-get install docker-compose
```

from the official repository or search for some alternative methods of installing more recent version than the one you currently have.



## Project setup

This process is available in two flavors:

1. Quick setup (if you can't wait to play)
2. Step by step approach (has additional explanations)

Either way the end result that can be checked by the command:

```sh
docker ps -a --format '{{.Image}} {{.Status}} {{.Ports}}'
```

 should be:

| IMAGE                          | STATUS                 | PORTS                                               |
| ------------------------------ | ---------------------- | --------------------------------------------------- |
| carrental_nginx:1.17.8         | Up 9 minutes (healthy) | 0.0.0.0:9090->80/tcp, :::9090->80/tcp               |
| carrental_composer:2.2.7       | Up 9 minutes           |                                                     |
| carrental_php:8.1.3-fpm-buster | Up 9 minutes           | 9000/tcp                                            |
| carrental_elasticsearch:7.17.1 | Up 9 minutes (healthy) | 0.0.0.0:9200->9200/tcp, :::9200->9200/tcp, 9300/tcp |

 [^TOC^](#Table-of-contents)

## Quick setup (without Docker cleanup)

1. Clone this repository:

   ```sh
   git clone 'https://github.com/212223/painless-car-rental.git'
   ```

3. Change directory:

   ```sh
   cd painless-car-rental/docker/service
   ```

3. Make `setup.sh` script executable && run `script.sh`:

   ```sh
   sudo chmod ug+x ./setup.sh && ./setup.sh
   ```

   `./setup.sh` may ask you for the sudo password because it is required for changing permissions to the directories it creates.

   Feel free to check `./setup.sh` content if you are wary, run:

   ```sh
   cat ./setup.sh
   ```

   

   **Do not** run `./setup.sh` as a sudo user. Wrong, do **not** do this:

   ```sh
   sudo ./setup.sh
   ```

   because it needs to run as your regular user in order to use your user id and group id and make correct ownership of directories.

   

   If you see an error like this:

   > Got permission denied while trying to connect to the Docker daemon socket at unix:///var/run/docker.sock: Get "...": dial unix /var/run/docker.sock: connect: permission denied

   This means that your user don't have permissions to run docker.

   Add your user to the `docker` group, run:

   ```sh
   sudo usermod -a -G docker $USER
   ```

   Reload docker:
   
   ```sh
   sudo systemctl restart docker
   ```

   Relog your user:
   
   ```sh
   su - $USER
   ```

   Check if your user is added to the `docker` group, run:
   
   ```sh
   groups
   ```

   example terminal output:
   
   ```
   test sudo docker
   ```

   Once you see `docker` group then run setup script again (without sudo):
   
   ```sh
   ./setup.sh
   ```

   
   
   If you see an error:

   > * error listing credentials - err: exit status 1, out: `Error from list function in secretservice_linux.c likely due to error in secretservice library`

   Install these packages:

   ```sh
   sudo apt-get install -y pass gnupg2
   ```

   
   
4. Open your browser and go to:
   http://localhost:9090
   
4. Read the section [How to play with it?](#how-to-play-with-it)

5. Read about the project structure in [./docker/service/README.md](./docker/service/README.md)

 [^TOC^](#Table-of-contents)

## Step by step setup



#### Clone repository and create directories

Clone this repository && change dir to it:

```sh
git clone 'https://github.com/212223/painless-car-rental.git' && cd painless-car-rental/docker/service
```



Export your current shell user id and its group id, run:

```sh
export uid=$(id -u)
export gid=$(id -g)
```

to have all `docker-compose` commands run as your current user and own files.

If not then they will be run as user:group = 1000:1000 or 1000:0 for Elasticsearch.



Own all directories and files:

```sh
sudo chown -R $uid:$gid ../../
```



Add execution for the current user and its group to init scripts

```sh
sudo chmod -v ug+x,o-wx \
../../app/default/.elasticsearch-http-requests/request.sh \
../../app/default/.elasticsearch-http-requests/init.sh
```



If the docker attempts to mount at HOST a directory that does not exists it will be created by the docker demon. 

The problem is that directory will be created by docker that runs as a root therefore it will be owned by the user id = 0. 

That directory will be not accessible for all of the processes that run within containers with different user id.

This application runs all containers with the same user id and group id as the user who started them.

Because of that it is possible to freely use PHP composer for example without any sudo as well deleting all files without any sudo.

Running containers as a regular user also enhances security and makes possible to use file access permissions different for user, group and others (which makes no difference if the user is root).

In order to have access permissions from within containers that run processes as a regular user all of the directories that are used as host's mounting points must be created upfront and have desired permissions before running the docker commands.



Create directory for Nginx:

```sh
mkdir -p \
./nginx-v1/image/files/var/log/nginx \
./nginx-v1/image/files/usr/share/nginx/html/default && \
\
sudo chown -R $uid:$gid \
./nginx-v1/image/files/var/log/nginx \
./nginx-v1/image/files/usr/share/nginx/html/default && \
\
sudo chmod -v -R ug=rwX,o=rX \
./nginx-v1/image/files/var/log/nginx \
./nginx-v1/image/files/usr/share/nginx/html/default
```



Create directory for Elasticsearch

```sh
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
```


 [^TOC^](#Table-of-contents)

## Introduction to docker and docker-compose

Feel free to use `--help` option before running any `docker` or `docker-compose` command to know what you are doing.

run:

```sh
docker --help
```

or:

```sh
docker-compose --help
```

to get information about `docker` or `docker-compose` commands.



You may get also use `--help` for the info related to a specific sub-command:

```sh
docker ps --help
```

or:

```sh
docker-compose ps --help
```



The command `docker` should work without any problem anywhere.

However using `docker-compose` commands usually requires your shell to be inside the directory where the `docker-compose.yaml` file is ([./docker/service](./docker/service)  for this project).



Note that if you use sub commands then command-line options switches are available (and potentially different) for both commands for eg. `docker-compose` and its subcommand `up`

```sh
docker-compose --help
```

will display different option switches than

```sh
docker-compose up --help
```

keep that in mind and place option switches right after the command they relate to. for example:

wrong:

```sh
docker-compose up --build --profile dev
```

terminal output:

> unknown flag: --profile

subcommand `up` of the `docker-compose` doesn't have the `--profile` switch therefore `--build` option must be placed right after the `docker-compose` command.

correct:

```sh
docker-compose --profile dev up --build
```

 [^TOC^](#Table-of-contents)

## Docker cleanup (optional)

Before running this application you may want to do the Docker cleanup and remove all of the containers and networks.

Make sure you that you do not have any: 

- container
- docker network
- dandling images
- dandling build cache

that is important (ie a container(s) handling an incoming traffic or saving a backup data) before proceeding with the steps as described in this section.

**Running** the **Docker cleanup** section commands **is optional** but should give you a clean starting point and lack of confusion by having  only the containers and networks that are related to this project.

 [^TOC^](#Table-of-contents)

#### Containers cleanup

To see if you have any containers created run:

```sh
docker ps -a
```

During the development time you may stop all containers, run:

```sh
docker-compose stop
```

Then you may remove all of them, run:

```sh
docker-compose down
```

But then still some containers may be presented by `docker ps -a` command. If you want to remove all of the containers, even those that are not related to this application project then run:

```sh
docker rm $(docker ps -aq)
```

In case of any complains from docker you may add flag `--force` but read and understand what the docker is complaining on and decide if you really want to force docker to remove all of the containers:

```sh
docker rm --force $(docker ps -aq)
```

to see effect run again:

```sh
docker ps -a
```

terminal output:

```
CONTAINER ID   IMAGE     COMMAND   CREATED   STATUS    PORTS     NAMES
```

should show no containers (despite their state: created, stopped, running, exited etc.)

 [^TOC^](#Table-of-contents)

#### Networks cleanup

You may also remove all of the docker networks that you don't need any longer including networks used by the docker containers you've just removed.

to list all of the networks run:

```sh
docker network ls
```

to remove all of the user networks:

```sh
docker network rm $(docker network ls -q --filter 'type=custom')
```

run:

```sh
docker network ls 
```

terminal output:

```
NETWORK ID     NAME      DRIVER    SCOPE
5d5de2f7ccd4   bridge    bridge    local
b75e841984e3   host      host      local
efff0416acdb   none      null      local
```

should show that there are no other networks than the default ones.

 [^TOC^](#Table-of-contents)

#### Volumes cleanup

This application does not make any use of volumes (besides the ones that are defined by the images' Dockerfiles). Therefore it is not needed nor recommended to do a cleanup of them for the purpose of running this application.

 [^TOC^](#Table-of-contents)

#### Development time cleanup

At first time of application run it is not necessary to follow this instruction - you may skip it.

Once you start working with this application and modify it, stop,  remove, build and run again you may find that you have some containers or networks that are not used but still present when you issue the `docker ps -a` or `docker network ls` command.

In such case, you may find useful 

Following steps from [Run, Stop, Remove](#run-stop-remove) to run and do all cleanup after shutting the application down.

or using this command:

```sh
docker system prune
```

that outputs in the terminal:

```
WARNING! This will remove:
  - all stopped containers
  - all networks not used by at least one container
  - all dangling images
  - all dangling build cache

Are you sure you want to continue? [y/N]
```

The terminal output is pretty self explanatory however if you would like to check the documentation:

- [docker system prune](https://docs.docker.com/engine/reference/commandline/system_prune/)
- [docker container prune](https://docs.docker.com/engine/reference/commandline/container_prune/)
- [docker network prune](https://docs.docker.com/engine/reference/commandline/network_prune/)

 [^TOC^](#Table-of-contents)

## Docker compose project setup

#### Start services

Do not run any of the commands with `sudo` unless it is clearly used.

If you run these commands with `sudo` then the user id will be 0 as well as the group id. That will affect all of the containers that are built and used with the same user id and group id as the user that issued the commands to built and run them.

You will have no benefit of running the commands with `sudo` but only will degrade security and make setting access permissions for user, group and others pointless.

If you have any problems running docker commands without `sudo` then you need to add your user to the docker group, read about that in the [Quick setup (without Docker cleanup)](#quick-setup-without-docker-cleanup) section.

run:

```sh
docker-compose --profile dev up --detach --build --force-recreate
```

to get the  `dev` profile running and wait approx 40 sec to finish.

Once done and shell is accepting new commands, terminal output:

```
[+] Running 4/4
 ⠿ Container carrental-php-composer-v2-1   Started  2.2s
 ⠿ Container carrental-elasticsearch-v7-1  Healthy 27.1s
 ⠿ Container carrental-php-fpm-v8-1        Started  3.0s
 ⠿ Container carrental-nginx-v1-1          Started 28.0s
```

 

run:

```sh
docker ps -a --format '{{.Image}}\t{{.Status}}\t{{.Names}}'
```

to see all containers created, terminal output:

```
carrental_nginx:1.17.8	Up 7 seconds (healthy)	carrental-nginx-v1-1
carrental_php:2.2.7-composer	Up 31 seconds	carrental-php-composer-v2-1
carrental_php:8.1.3-fpm-buster	Up 30 seconds	carrental-php-fpm-v8-1
carrental_elasticsearch:7.17.1	Up 29 seconds (healthy)	carrental-elasticsearch-v7-1
```

with both `carrental-nginx-v1-1` and `carrental-elasticsearch-v7-1` status **(healthy)**

for the full info you may run:

```sh
docker ps -a
```



or open a new terminal window and run:

```sh
docker stats
```

that should show containers and their resources usage.

If you have opened the second terminal window, go back to the first terminal window and continue with instructions:

 [^TOC^](#Table-of-contents)

#### Install composer dependencies

```sh
docker-compose exec php-composer-v2 composer install
```

terminal output:

```
Generating optimized autoload files
38 packages you are using are looking for funding.
Use the `composer fund` command to find out more!

Run composer recipes at any time to see the status of your Symfony recipes.

Executing script cache:clear [OK]
Executing script assets:install public [OK]
```

At this moment the PHP application is set but there is still missing:

 [^TOC^](#Table-of-contents)

## Elasticsearch index creation and population

#### Make scripts executable

**Make `request.sh` script executable**

```sh
docker-compose exec php-composer-v2 /bin/sh -c "chmod -cv ug+x /app/default/.elasticsearch-http-requests/request.sh"
```

terminal output:

```
... 0754 (rwxr-xr--)
```

so your user and his group are able to execute that script.

 [^TOC^](#Table-of-contents)

**Make `init.sh` script executable**

```sh
docker-compose exec php-composer-v2 /bin/sh -c "chmod -cv ug+x /app/default/.elasticsearch-http-requests/init.sh"
```

terminal output:

```
... 0754 (rwxr-xr--)
```

so your user and his group are able to execute that script.

 [^TOC^](#Table-of-contents)

**There are at least three ways to create Elasticsearch's index and populate it**

- Quick setup with `init.sh` - just gets you the DB set.
- Step by step with `request.sh` - provides more fun.
- Step by step other methods (require manual edit of file(s))

 [^TOC^](#Table-of-contents)

#### Quick setup with init.sh

```sh
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/init.sh"
```

Terminal output:

```
...

Performing cURL request:
curl -sSL -X GET 'http://elasticsearch-v7:9200/cars/_count'
{"count":39,"_shards":{"total":1,"successful":1,"skipped":0,"failed":0}}
Success.
Done!
```

That's it. Open your bowser and visit http://localhost:9090/ 

 [^TOC^](#Table-of-contents)

#### Step by step setup with request.sh

##### Create index

```sh
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/request.sh /app/default/.elasticsearch-http-requests/Index/Create.http"
```

terminal output:

```
Performing cURL request:
...
{"acknowledged":true,"shards_acknowledged":true,"index":"cars"}
Success.
```

 [^TOC^](#Table-of-contents)

##### Populate index with data

```
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/request.sh /app/default/.elasticsearch-http-requests/Index/Bulk.http"
```

terminal output:

```
Performing cURL request:
curl -sSL -X POST -H 'Content-Type: application/x-ndjson' --data-raw \
'{"update":{"_index":"cars","_type":"_doc","_id":"1"}}
{"doc":{"producer":"Ferrari","model":"SF90","picture":"ferrari-sf90.jpg","production_year":2021,"colors":["red"],"service_ids":[2,4,6,8]},"doc_as_upsert":true}

...

{"update":{"_index":"cars","_type":"_doc","_id":"39","_version":1,"result":"created","_shards":{"total":2,"successful":1,"failed":0},"_seq_no":38,"_primary_term":1,"status":201}}]}
Success.
```

 [^TOC^](#Table-of-contents)

##### Get count of items

```sh
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/request.sh /app/default/.elasticsearch-http-requests/Index/Count.http"
```

terminal output:

```
Performing cURL request:
curl -sSL -X GET 'http://elasticsearch-v7:9200/cars/_count'
{"count":39,"_shards":{"total":1,"successful":1,"skipped":0,"failed":0}}
Success.
```

 [^TOC^](#Table-of-contents)

##### Search

```sh
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/request.sh /app/default/.elasticsearch-http-requests/Search/Phrase.http"
```

terminal output:

```
Performing cURL request:
curl -sSL -X POST -H 'Content-Type: application/json' --data-raw \
'{
  "_source": true,
  "explain": false,

...

{"producer":"McLaren","model":"720S","picture":"mclaren-720s.jpg","production_year":2018,"colors":["blue","orange","black"],"service_ids":[7,2,1,3]}}]}}
Success.
```

That's it. Open your bowser and visit http://localhost:9090/  and

Read the section [How to play with it?](#how-to-play-with-it).

 [^TOC^](#Table-of-contents)

##### Delete index

```sh
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/request.sh /app/default/.elasticsearch-http-requests/Index/Delete.http"
```

terminal output:

```
Performing cURL request:
curl -sSL -X DELETE 'http://elasticsearch-v7:9200/cars'
{"acknowledged":true}
Success.
```

Terminal may print `FAIL!` in case there was no index to delete (you deleted it before, or did not create).

 [^TOC^](#Table-of-contents)

#### Step by step other methods

All HTTP requests can be executed either:
- from within [PhpStorm's built-in REST HTTP client](https://www.jetbrains.com/help/phpstorm/http-client-in-product-code-editor.html) (samples in [.elasticsearch-http-requests directory](https://github.com/lrynek/phpers-2021/blob/main/.elasticsearch-http-requests))
- in [Insomnia REST HTTP client](https://insomnia.rest/) (import [insomnia.json file](https://github.com/lrynek/phpers-2021/blob/main/insomnia.json) with all the samples)

You may receive an error from Elastisearch that complains about nested document. If so then add this query

```
?include_type_name=true
```

to your `PUT` request like to this one: `app/default/.elasticsearch-http-requests/Index/Create.http`

first line:

```http
PUT http://localhost:9200/cars
```

should become:

```http
PUT http://localhost:9200/cars?include_type_name=true
```

Keep in mind that using `localhost` as a request's host name is only valid if you make that request from your machine (not from the composer docker container). 

Therefore if you want to do them manually but from a docker container you have two choices:

- Make requests from within the Elasticsearch container because there the `localhost` points to the Elasticsearch's service internal [loopback](https://en.wikipedia.org/wiki/Loopback).
- Change the `localhost` in the request to `elasticsearch-v7` or to the Elasticsearch container name available under `docker ps` or Elasticsearch service network alias that is available by using the command: `docker inspect elasticsearch-container-name` 



Open your bowser and visit http://localhost:9090/ 

 [^TOC^](#Table-of-contents)

## How to play with it?

Read about the project structure in [./docker/service/README.md](./docker/service/README.md)

or continue with the content as below:

 [^TOC^](#Table-of-contents)

## Docker

 [^TOC^](#Table-of-contents)



Before doing any commands from this section make sure that your terminal's current directory is  [./docker/service](./docker/service) 



#### Run all services

To run this application you need to specify explicitly the profile you want to run.

Profile `dev` runs all of the available services. To run the `dev` profile type:

```sh
docker-compose --profile dev up
```

Note `--profile` option comes after the `docker-compose` command, not after the `up` subcommand.

Your terminal shell will be locked and you will not be able to type into it but shell will print logs from any service container instead:

```
carrental-php-fpm-v8-1        | [17-Mar-2022 15:08:41] NOTICE: fpm is running, pid 1
carrental-php-fpm-v8-1        | [17-Mar-2022 15:08:41] NOTICE: ready to handle connections
carrental-elasticsearch-v7-1  | {"type": "server", "timestamp": "2022-03-17T15:08:42,246Z"
```

 [^TOC^](#Table-of-contents)

##### Release the shell lock

Use the keyboard combination of <kbd>Ctrl</kbd>+<kbd>c</kbd>.

The <kbd>Ctrl</kbd>+<kbd>c</kbd> stops all of the containers created by `docker-compose` `up` 

```
[+] Running 4/4
⠿ Container carrental-nginx-v1-1          Stopped
⠿ Container carrental-elasticsearch-v7-1  Stopped
⠿ Container carrental-php-composer-v2-1   Stopped
⠿ Container carrental-php-fpm-v8-1        Stopped
```

and gives the shell control back to you.

 [^TOC^](#Table-of-contents)

#### Run all services without shell locking

If you don't want `docker-compose` `up` to hold your shell you may use `--detach` or `-d` flag.

```sh
docker-compose --profile dev up --detach
```

after starting all of the services:

```
[+] Running 6/6
 ⠿ Network carrental_backend               Created 0.1s
 ⠿ Network carrental_frontend              Created 0.1s
 ⠿ Container carrental-php-fpm-v8-1        Started 1.9s
 ⠿ Container carrental-php-composer-v2-1   Started 2.9s
 ⠿ Container carrental-elasticsearch-v7-1  Healthy 24.4s
 ⠿ Container carrental-nginx-v1-1          Started 25.6s
```

the shell will be released and waiting for your input.

However the downside is that you will not have the terminal output view with information coming from the services like it was printed without using the `--detach` flag.

To solve that issue you run this command preferably in a new terminal window:

```sh
docker-compose logs
```

that will provide you the same output as running the `docker-compose` `up` that locks the shell.

When running `docker-compose` `up` with `--detach` key combination <kbd>Ctrl</kbd>+<kbd>c</kbd> will not stop and remove containers with their networks.

To address that issue check commands from sections as below:

- [Stop services](#Stop-services) (equivalent of <kbd>Ctrl</kbd>+<kbd>c</kbd>)
- [Remove service containers](#Remove-service-containers)
- [Stop & remove service containers with their network](#stop--remove-service-containers-with-their-network) 

 [^TOC^](#Table-of-contents)

#### Run a specific profile

Services and profiles that they are assigned to are listed in [./docker/service/_profiles.yaml](./docker/service/_profiles.yaml)



This section assumes you have created containers by steps from [Run all services](#Run-all-services) and stopped them as  described in: [Release the shell lock](#Release-the-shell-lock).

If you want to run a specific profile you know that you need to specify `--profile` option followed by the profile name.

Let's run `cli` profile that has only the `php-composer-v2` service assigned to it, run:

```sh
docker-compose up --profile cli
```

terminal output:

```
[+] Running 1/0
 ⠿ Container carrental-php-composer-v2-1  Created 0.0s
Attaching to carrental-elasticsearch-v7-1, carrental-nginx-v1-1, carrental-php-composer-v2-1, carrental-php-fpm-v8-1

...
```

Did you notice something strange?

If not then open a new terminal window <kbd>Ctrl</kbd>+<kbd>Alt</kbd>+<kbd>t</kbd> and run:

```sh
docker ps -a
```

Check how many containers are running, it should be there just one: `carrental-php-com
poser-v2-1` because that's the only service the profile `cli` has.

It looks that by running `docker-compose --profile cli` command we end up having all of the containers running from the `dev` profile. Why is that?

Notice terminal output of the `docker-compose --profile cli`, especially the line at the beginning:

> [+] Running 1/0
>  ⠿ Container carrental-php-composer-v2-1  Created 0.0s

That looks as expected, we wanted to create just one service that the `cli` profile consist of but besides that also something else has happened:

> Attaching to carrental-elasticsearch-v7-1, carrental-nginx-v1-1, carrental-php-composer-v2-1, carrental-php-fpm-v8-1

Use the keyboard combination of <kbd>Ctrl</kbd>+<kbd>c</kbd> to stop all of the containers.

Terminal output:

```
[+] Running 4/4
⠿ Container carrental-nginx-v1-1          Stopped
⠿ Container carrental-elasticsearch-v7-1  Stopped
⠿ Container carrental-php-composer-v2-1   Stopped
⠿ Container carrental-php-fpm-v8-1        Stopped
```

In order to have running only `carrental-php-composer-v2-1` [Remove service containers](#Remove-service-containers) 

and run profile `cli` again:

```sh
docker-compose up --profile cli
```

Terminal output:

```
[+] Running 2/2
 ⠿ Network carrental_backend              Created 0.1s
 ⠿ Container carrental-php-composer-v2-1  Created 0.1s
Attaching to carrental-php-composer-v2-1
```

As you can see there is only one container created and attaching is done to it. No other containers were attached because they did not exist (they were removed).



If you want always to run a specific profile then edit the [.env](./docker/service/.env) file and put there:

```
COMPOSE_PROFILES=dev
```

so you should be able to issue only the:

```sh
docker-compose up
```

command to run the `dev` profile.

All profiles that you may choose from are defined in the [_profiles.yaml](./docker/service/_profiles.yaml) file in the same directory where the docker-compose.yaml file is [./docker/service](./docker/service) 

Documentation: [Using profiles with Compose](https://docs.docker.com/compose/profiles/)

 [^TOC^](#Table-of-contents)

#### Stop services

It makes sense to use it only if you plan to restart these containers later.

If your intention is to remove services then use command provided in [Remove service containers](#Remove-service-containers) that may stop containers as well before removing them or use [Stop & remove service containers with their network](#stop--remove-service-containers-with-their-network) that is able to do the same plus networks removal.

If you want to only stop docker containers that were started by

```sh
docker-compose up
```

without `--deatach` flag then you need to press in that terminal <kbd>Ctrl</kbd>+<kbd>c</kbd>.

If the process of stopping containers takes too long then you may press  <kbd>Ctrl</kbd>+<kbd>c</kbd> again to kill containers. Keep in mind that may result in the data loss.

if you started containers with the`--deatach` flag then you need to issue a command

```sh
docker-compose stop
```

Example terminal output:

```
[+] Running 4/4
⠿ Container carrental-nginx-v1-1          Stopped
⠿ Container carrental-elasticsearch-v7-1  Stopped
⠿ Container carrental-php-composer-v2-1   Stopped
⠿ Container carrental-php-fpm-v8-1        Stopped
```

 [^TOC^](#Table-of-contents)

#### Remove service containers

You may remove service containers that are already stopped or even running containers if `--force` flag added:

```sh
docker-compose rm --force --stop
```

options:

- `-f`, `--force`    Don't ask to confirm removal
- `-s`, `--stop`      Stop the containers, if required, before removing

example terminal output:

```
[+] Running 4/4
 ⠿ Container carrental-php-composer-v2-1   Stopped 10.4s
 ⠿ Container carrental-nginx-v1-1          Stopped 0.6s
 ⠿ Container carrental-php-fpm-v8-1        Stopped 0.3s
 ⠿ Container carrental-elasticsearch-v7-1  Stopped 0.8s

Going to remove carrental-nginx-v1-1, carrental-elasticsearch-v7-1, carrental-php-fpm-v8-1, carrental-php-composer-v2-1

[+] Running 4/0
 ⠿ Container carrental-php-composer-v2-1   Removed 0.0s
 ⠿ Container carrental-nginx-v1-1          Removed 0.0s
 ⠿ Container carrental-elasticsearch-v7-1  Removed 0.0s
 ⠿ Container carrental-php-fpm-v8-1        Removed 0.0s
```

If you want to remove also networks then look at: [Stop & remove service containers with their network](#stop--remove-service-containers-with-their-network)

 [^TOC^](#Table-of-contents)

#### Stop & remove service containers with their network

[Remove service containers](#Remove-service-containers) only removes containers but what if you need to get rid of all of the containers with their networks for eg. you reconfigured network and want to do `docker-compose` `up` with the new network configuration?

You may use for that `docker-compose` `down` that is the opposite command to the `docker-compose` `up`, run:

```sh
docker-compose down --remove-orphans
```

Options:

- `--remove-orphans`    Remove containers for services not defined in the Compose file.

example terminal output:

```
[+] Running 6/6
 ⠿ Container carrental-php-composer-v2-1   Removed 10.5s
 ⠿ Container carrental-elasticsearch-v7-1  Removed 1.0s
 ⠿ Container carrental-php-fpm-v8-1        Removed 0.5s
 ⠿ Container carrental-nginx-v1-1          Removed 1.0s
 ⠿ Network carrental_backend               Removed 0.3s
 ⠿ Network carrental_frontend              Removed 0.2s
```

Note that networks:

```
 ⠿ Network carrental_backend               Removed 0.3s
 ⠿ Network carrental_frontend              Removed 0.2s
```

were also removed in contrary to the [Remove service containers](#Remove-service-containers) that removes only services.

 [^TOC^](#Table-of-contents)

#### Run, Stop, Remove

Make sure you that you do not have any: 

- container
- docker network

that is important (ie a container(s) handling an incoming traffic or saving a backup data) before proceeding with the steps as described in this section.



When developing an application it is very often need to do a repetitive cycle of docker commands:

1. Run all or just selected services
2. Shut everything down
3. Clean any leftovers
4. Build the images again
5. Repeat point 1 with updated configuration



Below is a one-liner that covers **points 1~4** for the `dev` profile (all services).

Run and cleanup this project:

```sh
docker-compose --profile dev up --build --force-recreate; \
docker-compose down --remove-orphans;
```

See description of commands below.

Run and cleanup every project:

```sh
docker-compose --profile dev up --build --force-recreate; \
docker-compose down --remove-orphans; \
containers="$(docker ps -aq)"; [ -z "$containters" ] || docker rm -f $containers; \
networks="$(docker network ls -q --filter 'type=custom')"; [ -z "$networks" ] || docker network rm $networks;
```

Explanation:

- `docker-compose --profile dev up --build --force-recreate` runs the `dev` profile (`--profile`) and builds (`--build`) all images always (`--force-recreate`). Blocks the terminal input (lack of `--deatch`) till a user presses <kbd>Ctrl</kbd>+<kbd>c</kbd>.

- `docker-compose down --remove-orphans` shuts all containers down and removes them with their networks.

- `containers="$(docker ps -aq)"; [ -z "$containters" ] || docker rm -f $containers;` removes all of the containers even these ones that are not related to the project.

- `networks="$(docker network ls -q --filter 'type=custom')"; [ -z "$networks" ] || docker network rm $networks` removes all of the user created networks even these that are not related to this project.

  

**Point 5**: To repeat that command use <kbd>Ctrl</kbd>+<kbd>Up Arrow</kbd>. Once it is displayed press <kbd>Enter</kbd>.

 [^TOC^](#Table-of-contents)

#### Build images

If you make any changes to the Dockerfiles you should use `--build` flag so the `docker-compose` `up` rebuilds your images before running them again.

example:

```sh
docker-compose --profile dev up --build
```

Sometimes even you have made changes to the Dockerfile or a service `.yaml` file of an image docker will use its old version, therefore `--force-recreate` is useful during the development phase:

```sh
docker-compose --profile dev up --build --force-recreate
```



If you run your `docker-compose` `up` command with newly build images but still see that the old image is used check with the command:

```sh
docker ps -a
```

name of the containers under the column `IMAGE`.

If the column has a hash value like: `26d6a19a43ba` instead of a container name for eg: `carrental_composer:2.2.7` then something went wrong and the docker has not started with the newly created image but with an old one.

In such case you may need to:

- Check if a new Dockerfile has any errors and if it was successfully build and tagged
- Follow steps from [Run, Stop, Remove](#run-stop-remove)
- Follow steps from [Development time cleanup](#Development-time-cleanup)

 [^TOC^](#Table-of-contents)

#### Composer

If you want to run any PHP composer command then you have two options:

exec command:

```sh
docker-compose exec php-composer-v2 composer install
```

or attach to the container:

```sh
docker attach carrental-php-composer-v2-1
```

then you will be logged into the `carrental-php-composer-v2-1` shell (`/bin/bash`) and inside the project directory so you will be able to type:

```sh
composer install
```

Note that example of attaching above used `docker` command instead of `docker-compose` therefore a full name of the container was used (`carrental-php-composer-v2-1`) instead of the service name (`php-composer-v2`)

To exit from the attached container just run:

```sh
exit
```

that will exit the container but since its restart policy is:

```yaml
  php-composer-v2:
    restart: unless-stopped
```

then it will be restarted, allowing you to attach to it again.

 [^TOC^](#Table-of-contents)

#### Monitoring

For monitoring is good to open a new terminal window and run:

```sh
watch -t -n 2 "docker ps -a"
```

then open another window and type:

```sh
docker stats -a
```

`docker stats` has issue that after some time there are lot of "dead" entries that are meaningless. In order to get rid of them you need to terminate that command with <kbd>Ctrl</kbd>+<kbd>c</kbd> and run it again.

To see logs (in case you run `docker-compose` `up` with the `-d` or `--detach` flag):

```sh
docker-compose logs
```

To list images releated to this project run:

```sh
docker image ls 'carrental_*'
```

 [^TOC^](#Table-of-contents)

#### Reinstallation

At any point of time you may run the `./setup.sh` script again as described in [Quick setup (without Docker cleanup)](#quick-setup-without-docker-cleanup).

You may do that for example to change the project's files and directories ownership to a different user. If you want to do that just switch to the user that should own that project and perform the `./setup.sh` again.

 [^TOC^](#Table-of-contents)

## Uninstallation

Stop and remove all of the containers:

```sh
docker-compose down --remove-orphans
```



list all of the containers' images:

```sh
docker image ls 'carrental_*'
```

If listed images are the one you expect you may remove them by running:

```sh
docker image rm -f $(docker image ls -q 'carrental_*')
```



if you see:

> Error response from daemon: conflict: unable to delete 8a9b37ed9ecc (cannot be forced) - image has dependent child images



remove dandling images:

```sh
docker rmi $(docker images --filter "dangling=true" -q --no-trunc)
```

and repeat command:

```sh
docker image rm -f $(docker image ls -q 'carrental_*')
```

again.

If you still can't remove an image, try by using its name:tag for eg.:

```sh
docker image rm -f carrental_php:8.1.3-fpm-buster
```



Remove the project directory with its files (you should not need `sudo` for this)

 [^TOC^](#Table-of-contents)

## Elasticsearch

**References:**

[Elasticsearch 7.17 Guide](https://www.elastic.co/guide/en/elasticsearch/reference/7.17/index.html)

[Elasticsearch 7.17 Guide - Docker](https://www.elastic.co/guide/en/elasticsearch/reference/7.17/docker.html)



#### Reinitialize

At any time when you messed up the example cars index you may re-create it and populate with a single command (your shell needs to be in the [./docker/service](./docker/service)  dir):

```sh
docker-compose exec php-composer-v2 /bin/sh -c "/app/default/.elasticsearch-http-requests/init.sh"
```

It deletes index, creates it, and populates with the example data.

terminal output:

```
...

Performing cURL request:
curl -sSL -X GET 'http://elasticsearch-v7:9200/cars/_count'
{"count":39,"_shards":{"total":1,"successful":1,"skipped":0,"failed":0}}
Success.
Done!
```

or you may do it step by step:

- [Delete index](#Delete-index)
- [Create index](#Create-index)
- [Populate index with data](#Populate-index-with-data)

An alternative way is to run the `./setup.sh` script again as described in [Quick setup (without Docker cleanup)](#quick-setup-without-docker-cleanup)

You may use also: [Step by step other methods](#Step-by-step-other-methods)

 [^TOC^](#Table-of-contents)

#### Elasticsearch code

All Elasticsearch implementation related code is placed in `src/Elasticsearch` directory.

The core ranking logic is built [from specific `Factors` classes](https://github.com/lrynek/painless-car-rental/tree/main/src/Elasticsearch/ValueObject/Factor):
- [`RawScoreFactor`](https://github.com/lrynek/painless-car-rental/blob/main/src/Elasticsearch/ValueObject/Factor/RawScoreFactor.php) that propagates the originally calculated document score to the overall scoring (as it is being overwritten / replaced by all custom functions) in order to weight it along with other custom factors provided by the developer
- [`DodgePromoFactor`](https://github.com/lrynek/painless-car-rental/blob/main/src/Elasticsearch/ValueObject/Factor/DodgePromoFactor.php) that promotes all documents that has `producer` field equal to `Dodge` (you can switch to any other)
- [`ColorRelevanceFactor`](https://github.com/lrynek/painless-car-rental/blob/main/src/Elasticsearch/ValueObject/Factor/ColorRelevanceFactor.php) that ranks higher these documents / cars which has more intensive or exclusive color to the ones that are being filtered out on every app's request

Then the `RecommendedSorter` that includes all those ranking factors is [set up in `CarRepository`](https://github.com/lrynek/painless-car-rental/blob/b4a8431ffd73c7417b00d6428ef491c91b45960f/src/Elasticsearch/Repository/CarRepository.php#L32) to guarantee it applies to every search request:

```php
<?php
// ...

final class RecommendedSorter implements FactorSorterInterface
{
  // ...

	public function __construct(private ?Factors $factors = null)
	{
		$this->factors ??= new Factors(
			new RawScoreFactor(new Weight(1)),
			new DodgePromoFactor(new Weight(100)),
			new ColorRelevanceFactor(new Weight(50)),
		);
	}

  // ...
}
```

💡 You can comment out any of the factors to see how they contribute to the ranking.

💡 You can add any other factor you want on base of those existing ones.

💡 You can also play with all those factors' weights as well in the `RecommendedSorter` constructor and see the influence on the overall ranking.

💡 In order to get rid of customly ranked results on the listing you can switch to `DefaultSorter` that sorts all results ascending by their `id`.

 [^TOC^](#Table-of-contents)

## Credits

[Ryszard](https://stackoverflow.com/users/1174405?tab=profile)

- Application conversion to docker-compose
- Setup and initialization scripts
- Docker and docker-compose related manuals

 [^TOC^](#Table-of-contents)

## Copyrights
Apart from [the project's LICENSE](https://github.com/lrynek/painless-car-rental/blob/main/LICENSE), [all car photo samples](https://github.com/lrynek/painless-car-rental/tree/main/public/images/cars) used in the project are taken from Google search results and all copyrights applies to their respective authors and shouldn't be used further than private/educational use without their explicit consent.
