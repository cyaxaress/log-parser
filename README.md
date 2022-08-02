# L1 Challenge Devbox

## Summary

- Dockerfile & Docker-compose setup with PHP8.1 and MySQL
- Symfony 5.4 installation with a /healthz endpoint and a test for it
- After the image is started the app will run on port 9002 on localhost. You can try the existing
  endpoint: http://localhost:9002/healthz
- The default database is called `database` and the username and password are `root` and `root`
  respectively
- Makefile with some basic commands

## Installation

```
  make run && make install
```

## Run commands inside the container

```
  make enter
```

## Run Migration
```
 php bin/console doctrine:schema:update --force
```

## Run tests

```
  make test
```

## Solution

This project uses two entities to organize data:
- LogFile: This entity used to hold information about log files. Information like, path to file, last check date and time, the cursor location
- LogRecord: represents each line of log file.

To parse log file run this code:
```
 php bin/console log:todb
```
By default, above command scans "log.txt" file in the root directory of project, but you can customize it by sending full path to your own log file like this:
```
php bin/console log:todb /logs/my-log.txt
```

after log file scanned you can send GET request to below end point:
```angular2html
/count
```

this end point also support this query parameters to filter data:
```angular2html
- serviceNames: array of service names
- statusCode: HTTP response code
- startDate: Timestamp
- endDate: Timestamp
```
please note that all query parameters are optional.

## Examples
Filter By status code
```angular2html
http://localhost:9002/count?statusCode=201
```

Between two dates
```angular2html
http://localhost:9002/count?startDate=2021-08-18T10:26:53.52Z&endDate=2021-08-21T00:00:00.52Z
```

Specific Service: 
```angular2html
http://localhost:9002/count?serviceNames[]=ORDER-SERVICE&serviceNames[]=HEMN-SERVICE
```


All together:
```angular2html
http://localhost:9002/count?startDate=2021-08-18T10:26:53.52Z&statusCode=201&serviceNames[]=ORDER-SERVICE&serviceNames[]=HEMN-SERVICE&endDate=2021-08-21T00:00:00.52Z
```
