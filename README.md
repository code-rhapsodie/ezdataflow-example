# Code Rhapsodie eZ Dataflow example

This projet is based on eZ Platform 2.5.

## Requirements

* A SGDB server (MySQL, MariaDB, or other...).
* PHP 7.1+
* composer
* yarn

## Install

Clone this repository or download an archive.

Open your favorite terminal, and go into `ezdataflow-example` folder.
Execute this command to install all vendor dependencies :

```shell script
$ php composer install -o
```

## Init the databases

Perform this command to initialize the database :

```shell script
$ php bin/console ezplatform:install clean
```

## Dataflow database schema

Execute this command to dump all necessary SQL queries:

```shell script
$ php bin/console code-rhapsodie:dataflow:dump-schema --update
```

Execute queries on your databases.

## Insert the City content type

Execute this command to execute all Kaliop migration available:

```shell script
$ php bin/console kaliop:migration:migrate 
```

## Try

Now you can use all command to try Dataflow bundle and eZ Dataflow bunble.

Try this command:

```shell script
$ php bin/console code-rhapsodie:dataflow:execute fc '{"url":"https:\/\/geo.api.gouv.fr\/communes?fields=nom,code,codesPostaux,codeDepartement,codeRegion,population&format=json&geometry=centre","content_type":"city","parent_location_id":57}'
```

After end of work, go to admin into [the folder "French cities"](http://127.0.0.1/admin/content/location/57) with the eZ Publish admin.
