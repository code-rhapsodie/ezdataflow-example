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

Before execute command, get  folder 'French city' location id from backoffice [http://127.0.0.1/admin/content/location/54#ez-tab-location-view-details#tab](http://127.0.0.1/admin/content/location/54#ez-tab-location-view-details#tab).

Now you can use all command to try Dataflow bundle and eZ Dataflow bunble.

Try this command:

```shell script
$ php bin/console code-rhapsodie:dataflow:execute fc '{"url":"https:\/\/geo.api.gouv.fr\/communes?fields=nom,code,codesPostaux,codeDepartement,codeRegion,population,centre&format=json&geometry=centre&limit=5","content_type":"city","parent_location_id":54}'
```

After end of work, go to admin into [the folder "French cities"](http://127.0.0.1/admin/content/location/57) with the eZ Publish admin.

To add a schedule from back office, the option must be in YAML format like this:

```yaml
url: 'https://geo.api.gouv.fr/communes?fields=nom,code,codesPostaux,codeDepartement,codeRegion,population,centre&format=json&geometry=centre&limit=100'
content_type: "city"
parent_location_id: 54
``` 


Usage of ArticleDataflowType

Debug :

```shell script
$ bin/console code-rhapsodie:dataflow:execute ia '{"url":"https:\/\/www.lemonde.fr\/rss\/une.xml","content_type":"short_news","parent_location_id":55}'
```

From admin UI

```yaml
url: 'https://www.lemonde.fr/rss/une.xml'
content_type: "short_news"
parent_location_id: 55
```