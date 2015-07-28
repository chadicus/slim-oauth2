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
curl -H 'Authorization: Bearer c859d2c9eec4800a6277671eba72a5a6f54f8693' http://localhost:8888/books
```

### Output
```
{
    "offset": 0,
    "limit": 5,
    "total": 11,
    "books": [
        {
            "id": "55b6bcdd498b62a2158b4567",
            "url": "/books/55b6bcdd498b62a2158b4567"
        },
        {
            "id": "55b6bcdd498b62a2158b4568",
            "url": "/books/55b6bcdd498b62a2158b4568"
        },
        {
            "id": "55b6bcdd498b62a2158b4569",
            "url": "/books/55b6bcdd498b62a2158b4569"
        },
        {
            "id": "55b6bcdd498b62a2158b456a",
            "url": "/books/55b6bcdd498b62a2158b456a"
        }
    ]
}
```

## Create a new book
```sh
curl -i -X POST -H Content-Type:application/json -H 'Authorization: Bearer c859d2c9eec4800a6277671eba72a5a6f54f8693' -d @examples/mongo/create.json http://localhost:8888/books
```

### Output

The body of the response will be empty.  The http status code will be `201` and there will a location header with the url for the new book `Location: /books/55b6be5e498b6293138b456f`

## Get the details of the newly created book

```sh
curl -H 'Authorization: Bearer c859d2c9eec4800a6277671eba72a5a6f54f8693' http://localhost:8888/books/55b6be5e498b6293138b456f
```

### Output
```
{
    "author": "Galos, Mike",
    "title": "Visual Studio 7: A Comprehensive Guide",
    "genre": "Computer",
    "price": 49.95,
    "publishDate": 987393600,
    "description": "Microsoft Visual Studio 7 is explored in depth, looking at how Visual Basic, Visual C++, C#, and ASP+ are integrated into a comprehensive development environment.",
    "id": "55b6be5e498b6293138b456f",
    "url": "/books/55b6be5e498b6293138b456f"
}
```

