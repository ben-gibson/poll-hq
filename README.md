PollHQ
===

A simplified project to explore event sourcing using a CQRS architecture pattern.


## Development

Below are the required steps to start development.

### Getting Started

#### Prerequisites

* Install [Docker](https://www.docker.com/) and Docker Compose.

#### Configure Environment Variables

Create a `.env` file from the existing `.env.dist` included in the repository.
 
```bash
$ cp .env.dist .env
```

Edit `DEV_UID` to match the uid from your host machine, which will make permission management easier between docker 
and your host machine.

```bash
$ sed -i "s/\(^DEV_UID=\)[^=]*$/\1$(id -u)/" .env
```

#### Run the containers

Run the containers in the background.

```bash
$ docker-compose up -d
```

#### Install dependencies

Run composer through the app container to download the projects dependencies. This will also run some important 
post-install commands such as database migrations.

```bash
$ docker-compose exec app composer install
```

#### Verify

If everything went successfully you should now be able to access the application on 
[http://localhost:8080](http://localhost:8080).

### Coding Standard

This project uses a customised version of the Doctrine coding standard that should be followed at all times. If you're 
using PHPStorm you can make following this standard easier by enabling the Code Sniffer inspection.

1. Navigate to `Settings > Editor > Inspections` and select `PHP > Quality Tools > PHP Code Sniffer validation` from the list.
1. Enable the inspection.
1. Set the severity to `ERROR` and check the show warning as checkbox with the option `WEAK WARNING`.
1. Select the coding standard `Custom` and point the rule set path to the `phpcs.xml.dist` file at the 
root of the project.

To check conformance to the standard, run the following command:

```bash
$ docker-compose exec app composer cs
```

There are some errors that can be automatically fixed by running the command below:

```bash
$ docker-compose exec app composer cs:fix
```


#### Xdebug

To use Xdebug update your `.env` file by uncommenting the `XDEBUG_CONFIG` variable and setting the `remote_host` value 
to the ip address of your host machine.

Next go to `Settings > Languages & Frameworks > PHP > Server` and create a new server named `pollhq-dev`. Set the host
to `localhost`, the port to `80`, and the debugger to `Xdebug`. Check the box labelled `Use path mappings` and map the 
project root to `/var/www`.

Now create a new `PHP Remote Debug` configuration (see [here](https://www.jetbrains.com/help/phpstorm/creating-and-editing-run-debug-configurations.html) for details) 
and name it `Xdebug`. Check the box labeled `Filter debug configuration by IDE key` and set the server as the one you just 
created and the IDE key as `PHPSTORM`.

You should now be able to debug the application using Xdebug. Add a breakpoint, navigate to PollHQ in your browser and 
check to see if it is caught in PHPStorm.