# Books API Example

There are currently three storage examples available.
* [PDO](http://php.net/manual/en/book.pdo.php)
* [Mongo (Legacy)](http://php.net/manual/en/book.mongo.php)
* [MongoDB](http://php.net/manual/en/set.mongodb.php)

In the example instructions below `pdo` can be replaced with `mongo` or `mongodb`

## Install dependencies and prepare the data storage
```sh
cd examples/pdo
composer install
./load.php
```

## Run the example app
```sh
cd ..
php -S localhost:8888 -t pdo pdo/index.php
```

## Obtain an access token
```sh
curl -X POST -d client_id=librarian -d client_secret=secret -d grant_type=client_credentials http://localhost:8888/token
```
### Output
```
{
    "access_token": "c859d2c9eec4800a6277671eba72a5a6f54f8693",
    "expires_in": 3600,
    "token_type": "Bearer",
    "scope": "bookCreate"
}
```

## Use the access token to search the books endpoint
```sh
curl -H 'Authorization: Bearer c859d2c9eec4800a6277671eba72a5a6f54f8693' http://localhost:8888/books?limit=2
```

### Output
```
{
    "offset": 0,
    "limit": 2,
    "total": 11,
    "books": [
        {
            "id": "58339e95d5200",
            "author": "Gambardella, Matthew",
            "title": "XML Developer's Guide",
            "genre": "Computer",
            "price": 44.95,
            "published": 970372800,
            "description": "An in-depth look at creating applications with XML."
        },
        {
            "id": "58339e95d5239",
            "author": "Ralls, Kim",
            "title": "Midnight Rain",
            "genre": "Fantasy",
            "price": 5.95,
            "published": 976942800,
            "description": "A former architect battles corporate zombies, an evil sorceress, and her own childhood to become queen of the world."
        }
    ]
}
```

## Create a new book
```sh
curl -i -X POST -H Content-Type:application/json -H 'Authorization: Bearer c859d2c9eec4800a6277671eba72a5a6f54f8693' -d @create.json http://localhost:8888/books
```

### Output
```
HTTP/1.1 201 Created
Host: localhost:8888
Connection: close
X-Powered-By: PHP/7.0.8-0ubuntu0.16.04.3
Content-Type: text/html; charset=UTF-8
Location: /books/5835a5df8a29c
Content-Length: 0
```
The body of the response will be empty.  The http status code will be `201` and there will a location header with the url for the new book `Location: /books/5835a5df8a29c`

## Get the details of the newly created book

```sh
curl -H 'Authorization: Bearer c859d2c9eec4800a6277671eba72a5a6f54f8693' http://localhost:8888/books/5835a5df8a29c
```

### Output
```
{
    "id": "5835a5df8a29c",
    "author": "Galos, Mike",
    "title": "Visual Studio 7: A Comprehensive Guide",
    "genre": "Computer",
    "price": 49.95,
    "published": 987393600,
    "description": "Microsoft Visual Studio 7 is explored in depth, looking at how Visual Basic, Visual C++, C#, and ASP+ are integrated into a comprehensive development environment."
}
```

## Use Authorization Code grant type

Navigate to [http://localhost:8888/authorize?response_type=code&client_id=librarian&state=xyz](http://localhost:8888/authorize?response_type=code&client_id=librarian&state=xyz)

### Output

![Authorize Form](https://raw.githubusercontent.com/chadicus/slim-oauth2/master/examples/form.png)

--

Click 'yes'

## Output

<h2>The authorization code is 437a8960671a751106196e8205979ab75c10db04</h2>

--

Request an access token using the authorization code

```sh
curl -u librarian:secret http://localhost:8888/token -d 'grant_type=authorization_code&code=437a8960671a751106196e8205979ab75c10db04'
```

## Output
```
{
    "access_token": "6c44c025a5a3116072e179c2893466b4c346a6b5",
    "expires_in": 3600,
    "token_type": "Bearer",
    "scope": null,
    "refresh_token": "1b0fde4b878bddbb955784fa447b19fca9558abd"
}
```

