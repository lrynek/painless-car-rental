## Table of contents

- [Project structure](#Project-structure)
- [Projects context](#Projects-context)
  - [Specific to this application context](#Specific-to-this-application-context)
  - [Generic service module context](#Generic-service-module-context)
    - [Copy a service module to any other project](#Copy-a-service-module-to-any-other-project)
    - [List of services modules](#List-of-services-modules)
    - [Service module volumes](#Service-module-volumes)
    - [Service module variables](#Service-module-variables)
    - [Service module volume has an application and module context](#Service-module-volume-has-an-application-and-module-context)
  - [Merging two contexts](#Merging-two-contexts) 
- [Health checks](#Health-checks)
  - [Nginx](#Nginx)
  - [Elasticsearch](#Elasticsearch)
- [Security](#Security)
  - [Permissions](#Permissions)
  - [Capabilities](#Capabilities)
  - [Network](#Network)
  - [Firewall](#Firewall)
  - [Vulnerability scan](#Vulnerability-scan)
- [Known Issues](#Known-Issues)
- Typical errors and their reasons
  - [Nginx page is loading forever](#Nginx-page-is-loading-forever)
  - [Elasticsearch exits with code 125](#Elasticsearch-exits-with-code-125)



## Project structure

There is a [dirlist.txt](./dirlist.txt) that shows all of the files.

[^TOC^](#Table-of-contents)

## Projects context

There are two contexts in this project:

- Specific to this application context
- Generic service module context

[^TOC^](#Table-of-contents)

### Specific to this application context

All files that start with underscore

```
_limit-memory.yaml
_network.yaml
_profiles.yaml
_restart-policy.yaml
_service-dependency.yaml
```

have code related specifically to this application.

for example [_service-dependency.yaml](_service-dependency.yaml) without comments:

```
services:
  nginx-v1:
    depends_on: 
      php-fpm-v8:
        condition: service_started
      elasticsearch-v7:
        condition: service_healthy
```

defines that Nginx server container should not run unless service `php-fpm-v8` started and service `elasticsearch-v7` started and reported its status as healthy. 

It makes no sense to run Nginx for this application unless it is able to pass requests to the PHP-FPM and make requests to the Elasticsearch service.

All of the services: `nginx-v1`, `php-fpm-v8` and `elasticsearch-v7` may work independently from each other in a different application. For example

- `nginx-v1` may serve only static files or work as a load balancer.
- `php-fpm-v8` may respond to other service's requests.
- `elasticsearch-v7` may be used only for Kibana.

Therefore their dependency is specific for this project and put in a separate file  [_service-dependency.yaml](_service-dependency.yaml) instead of putting the `depends_on` key into each of these service's yaml file.

[^TOC^](#Table of contents)

### Generic service module context

The key to understand that context is to focus on two words: `generic` and `module`.

The intention for this context is to be able to copy any service module and with minimal (perfectly none) modification have a base service for other application that may completely differ from this one.

Every module consist of two things:

- service directory
- service yaml file named the same as directory.

#### Copy a service module to any other project

If you want to copy elasticsearch-v7 module to your application then remember to copy the `elasticsearch-v7` directory and corresponding to it `elasticsearch-v7.yaml` file.

Currently docker does not support paths relative other than relative to the main docker-compose.yaml file therefore it would make a confusion if `elasticsearch-v7.yaml` file were put inside `elasticsearch-v7` directory and still need to use a path that is a relative to the `docker/service` dir.

[^TOC^](#Table-of-contents)

#### List of services modules

```
├── [DIR ]  elasticsearch-v7
├── [5.1K]  elasticsearch-v7.yaml
├── [DIR ]  nginx-v1
├── [4.4K]  nginx-v1.yaml
├── [DIR ]  php-composer-v2
├── [1.2K]  php-composer-v2.yaml
├── [DIR ]  php-fpm-v8
├── [ 767]  php-fpm-v8.yaml
```

There is a naming convention that every service module dir has a suffix of the **major only** version.

The reason is that for example `elasticsearch-v7` has the suffix `v7` so if currently Elasticsearch is used with the exact version `7.17.1` then having only the major version as a suffix allows you to bump the version number as long it does not brake compatibility and without any need to update dir name.

An additional benefit of that suffix is that you may have at the same project `elasticsearch-v7` and `elasticsearch-v8` without any conflicts and upgrade or downgrade them as you wish.

[^TOC^](#Table-of-contents)

#### Service module volumes

Example dir tree of `nginx-v1`:

```
.
└── [DIR ]  image
    ├── [  19]  .dockerignore
    ├── [   0]  .env.dev
    ├── [  19]  .gitignore
    ├── [ 602]  Dockerfile
    └── [DIR ]  files
        ├── [DIR ]  etc
        │   └── [DIR ]  nginx
        │       ├── [DIR ]  conf.d
        │       │   └── [ 894]  default.conf
        │       ├── [1007]  fastcgi_params
        │       ├── [2.8K]  koi-utf
        │       ├── [2.2K]  koi-win
        │       ├── [5.1K]  mime.types
        │       ├── [  22]  modules -> /usr/lib/nginx/modules
        │       ├── [1.0K]  nginx.conf
        │       ├── [ 636]  scgi_params
        │       ├── [ 664]  uwsgi_params
        │       └── [3.5K]  win-utf
        └── [ 166]  site.conf
```

All image files are stored in the subdir `image`, so you may add other related files like manuals into the dir of the `nginx-v1` service but without confusion if building an image from that service should have them or not. Simply - if files are in the `image` subdir then they are a subject of the build context.

`image/files` subdir has all of the files that should be either copied to the image by the Dockerfile `COPY` command or they should be mounted by a mount point. Important convention here is that all the files are in the directories that exactly mimic the file structure of the container that will be run from the image you build for example:

| HOST dir                   | Container dir |
| -------------------------- | ------------- |
| ./nginx-v1/files/etc/nginx | /etc/nginx    |

Sticking to this convention you will never have any doubts where files are mounted.

#### Service module variables

An example [php-fpm-v8.yaml](./php-fpm-v8.yaml) without comments:

```
version: "3.7"
services:
  php-fpm-v8:
    image: ${COMPOSE_PROJECT_NAME}_php:8.1.3-fpm-buster
    build: 
      context: ${service_path:-.}/php-fpm-v8/${image_path}
      args:
        image: php:8.1.3-fpm-buster
      shm_size: '2gb'
      target: dev

    env_file: 
      - "${service_path:-.}/php-fpm-v8/${image_path}/.env.dev"

    user: ${uid:-1000}:${gid:-1000}

    volumes:
      - ${app_path:-./app}:/usr/share/nginx/html
```

makes as any other service module use of variables:

- `${COMPOSE_PROJECT_NAME}` that is defined in the [.env](./.env) file and is `carrental` so all service's modules images for this project will have prefix of `carrental_`
- `${service_path:-.}` - defaults to the current directory `docker/service` but may be changed in the [.env](./.env) file. This is the path where docker-compose will look for the dir's of service modules for building them as well as for their dedictated env files.
- `user: ${uid:-1000}:${gid:-1000}` defines the user id and group id of the user that runs the image. For the images that have their own custom Dockerfile like [php-composer-v2/image/Dockerfile](./php-composer-v2/image/Dockerfile) the user id and group id is used for the image build.

[^TOC^](#Table-of-contents)

#### Service module volume has an application and module context

```
    volumes:
      - ${app_path:-./app}:/usr/share/nginx/html
```

Since they define both source (HOST) and destination (Container) mount points you need to decide if you want to have them inside a service module [php-fpm-v8.yaml](./php-fpm-v8.yaml) or create new application context file for eg. `_volumes.yaml` and put volumes definition there.

Because some of the services need volumes or bind points for their basic operation (persistent storage for databases) and their paths differ for every service then a decision was made to put volumes inside the service module so if you copy it to another project then you will always have the bare minimum paths on the service side defined and will need to adjust the host side to your needs. That seems to be more reasonable then searching over all of your projects where a particular service was used in order to acquire a knowledge about required paths for its persistence.

[^TOC^](#Table-of-contents)

### Merging two contexts 

The [.env](./.env) file has the entry:

```
COMPOSE_FILE=_network.yaml:_restart-policy.yaml:_limit-memory.yaml:_service-dependency.yaml:_profiles.yaml:docker-compose.yaml:php-composer-v2.yaml:elasticsearch-v7.yaml:php-fpm-v8.yaml:nginx-v1.yaml
```

That has a list of yaml files that are merged when you run for example:

```sh
docker-compose up
```

removing a file from that variable will result in not having it in the merged version that is used by the `docker-compose` to run this app.

However using [_profiles.yaml](./_profiles.yaml) is much more elegant way to have particular services up. Read about: **Run a specific profile** section of the [../../README.md](../../README.md)

[^TOC^](#Table of contents)

## Health checks

They are implemented in the services' Dockerfiles


### Nginx
[Dockerfile](./nginx-v1/image/Dockerfile)

Nginx in the free version don't have any sophisticated health check endpoint therefore a simple one was defined in its [default.conf](./nginx-v1/image/files/etc/nginx/conf.d/default.conf)

[^TOC^](#Table of contents)

### Elasticsearch

[Dockerfile](./elasticsearch-v7/image/Dockerfile)

This sevice has a health check endpoint defined by its vendor. Note that for a single node configuration the health check status as per documentation will never be `green` but at most operable state it will be `yellow`. 

If Elasticsearch is in multi node mode then the health check's status may be `green` or `yellow` depending on the number of nodes that are ok.

Current health check implementation returns status healthy in case either `green` or `yellow`.

[^TOC^](#Table-of-contents)


## Security

### Permissions

Do not run these containers as `root`, there was a significant effort done in order to make them work as a regular user. You may change the user to any and run the `setup.sh` script so it will change the file permissions of all of the files and initialize Elasticsearch db for you.

However if for any reason you want to run this application as a `root` then all you need is to call `setup.sh` script with `sudo` in front of it.

[^TOC^](#Table-of-contents)

### Capabilities

Look at the [nginx-v1.yaml](#./nginx-v1.yaml) `cap_drop` and `cap_add` sections.

Do not underestimate the importance of having at least basic security features implemented.

If you are going do develop your application then play with these values as described in the comments because no one better knows than you if your application is working as expected when restrictions are applied.

For some of the services adding limitations of the capabilities is a piece of cake. Try it!

[^TOC^](#Table-of-contents)

### Network

For your convenience the `elasticsearch-v7` service is available under the host's `localhost`. When releasing this application to the public domain, remember about disabling that by making the `backend` network internal in [_network.yaml](./_network.yaml)

[^TOC^](#Table-of-contents)

#### Firewall

It would not hurt to use a firewall for the services. You may use UFW or IPTables to limit the exposure of the services since if an image exposes any port it is currently not supported by docker to override that with your own Dockerfile and disable a port exposure.

[^TOC^](#Table-of-contents)


### Vulnerability scan

It is complety up to you to do it. No scan was made. 

[^TOC^](#Table-of-contents)

## Known Issues

`-` or `.` is not allowed for the variable name

`-` or `.` is not allowed for the variable value, workaround to put value between a single quote `'` or double quote `"` results in passing that variable with the quotes (not just with the content between them).

[^TOC^](#Table-of-contents)

## Typical errors and their reasons

### Nginx page is loading forever

or log error:

> nginx: [alert] could not open error log file: open() "/var/log/nginx/error.log" failed (13: Permission denied)

solution:

make sure that nginx has at least minimum permissions in its service yaml:

```yaml
cap_drop:
 - ALL

cap_add:
 - CAP_CHOWN
 - CAP_DAC_OVERRIDE
 - CAP_DAC_READ_SEARCH
 - CAP_FOWNER
 - CAP_SETGID
 - CAP_SETUID
```

[^TOC^](#Table-of-contents)

### Elasticsearch exits with code 125

**Solution**

Elasticsearch don't have enough permissions to access its files.

[^TOC^](#Table-of-contents)
