# php/nginx/mysql Docker Skeleton
Simple skeleton for containerizing your php application using nginx/php-fpm and works great for local development.

## Install Docker
For this to work you have to have docker installed with a daemon running. If you can't run `docker info` on the command line this will never work. Install docker for desktop if youre using mac/windows and docker if you're on linux.

## Building the Base Image
Start by building your php-base image, the [php docker](https://hub.docker.com/_/php) rig compiles everything inline so it's just easier and faster to get that all done once and then copy your php code into the base image. If you need extra packages you can always add them and make a new base image. The following docker command will make the php-base image we will extend from in the main Dockerfile. 

```
docker build --rm -f ./Dockerfile.php -t php-base .
```

## Running
The docker-compose.yml file has everything you need to start the sample app and connect php/nginx/mysql via docker networking. The trick is the services are available via dns with their simple names e.g. nginx, php, db so nginx has it's config set to call the php-fpm backend at `php:9000` and this is set as an environment variable if you move the container to say kubernetes where you might use different naming conventions.

```
docker-compose up --build
```

You will now have [http://localhost:8080](http://localhost:8080) pointed to port 80 on the nginx container and a docker ps which looks something like this...

```
> docker ps
CONTAINER ID   IMAGE                     COMMAND                  CREATED          STATUS          PORTS                               NAMES
31694084f9f4   danday74/nginx-lua        "nginx -g 'daemon of…"   30 minutes ago   Up 29 minutes   443/tcp, 0.0.0.0:8080->80/tcp       php-docker-skeleton_nginx_1
a0b5b24f42b2   php-docker-skeleton_php   "docker-php-entrypoi…"   30 minutes ago   Up 29 minutes   9000/tcp                            php-docker-skeleton_php_1
c3b06a217c0f   mysql:5.7                 "docker-entrypoint.s…"   32 minutes ago   Up 29 minutes   0.0.0.0:3306->3306/tcp, 33060/tcp   php-docker-skeleton_db_1
```

## Github Workflow
There's a sample Github action which you can use to build images on tag and push them into a gitlab repository and auto deploy them to a kubernetes server. Requirements are secrets in your github repository of a gitlab api token and the contents of a kubernetes config yaml file base64 encoded. This means your workflow can be `git tag v1.0.0 && git push origin v1.0.0` and the action will make your images and run helm to update the deployments to upgrade their image.

For this to work you will need to build the php-base image and push it into gitlab to something like `registry.gitlab.com/myapp/app/base:latest` and update your Dockerfile to extend `FROM` the full registry.

## Helm Chart
Included is a sample helm chart for how you could deploy an app and use secrets to store your database credentials. The chart will make a deployment which will contain one pod with the nginx/php-fpm containers running inside. This assumes you are using an external database and you pass the credentials into your app to connect via environment variables much like the docker file.

## Composer
Since youre now running inside a container you can't run composer from your host and will need to exec into the container to run it. Since the directories are mounted any changes composer makes to the file system will show up on your host and you can use them in your normal workflows like checking them into git. Included are a ps1 and bash script so you can run `./composer` like you normally would and it will pass the args and run inside the container. This makes it so you will always run composer with the version of php that is inside the container and ultimately in production.

## Notes
* For local dev the files are mounted from your host system into the container you can edit files and refresh your browser to see changes.
* nginx is used to serve static files like images/css/js and this is why the Dockerfile.nginx needs the COPY command, you will always build two images with this setup.
* The helm chart is untested, but I do use something similar in production today and copy/pasta'ed it... should be close enough.