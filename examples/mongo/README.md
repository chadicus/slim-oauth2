# Books API Example

## Load the book data
```sh
./examples/mongo/load.php
```

## Run the example app
```sh
php -S localhost:8888 -t examples/mongo examples/mongo/index.php
```

## Obtain an access token
```sh
curl -X POST -d client_id=librarian -d client_secret=secret -d grant_type=client_credentials http://localhost:8888/token
```

## Use the access token to search the books endpoint
```sh
curl -H 'Authorization: Bearer access_token' http://localhost:8888/books
```
