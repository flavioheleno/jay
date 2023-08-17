# Jay

Jay is a thin wrapper for json & simdjson, allowing the fastest available json decoder to be used in a transparent way.

Under the hood, **Jay** will pick [simdjson](https://github.com/crazyxman/simdjson_php) if available and the json
encoded string is up to 4GiB (4.294.967.295 bytes), otherwise it will fallback to PHP's JSON Core Extension.

## Installation

To use Jay, simple run:

```bash
composer require flavioheleno/jay
```

## Usage

This library usage is straightforward.

```php
$jsonEncoded = '{"a":"b","c":true,"d":10}';

// before
$jsonDecoded = json_decode($jsonEncoded, true);

// after
$jsonDecoded = Jay\Json::fromString($jsonEncoded, true);
```

```php
// before
$jsonEncoded = filge_get_contents('path/to/file.json');
$jsonDecoded = json_decode($jsonEncoded, true);

// after
$jsonDecoded = Jay\Json::fromFile('path/to/file.json', true);
```

## API

```php
/**
 * @param string $path      Name of the file to read
 * @param bool $associative When true, JSON objects will be returned as associative arrays; when
 *                          false, JSON objects will be returned as an instance of stdClass
 * @param int $depth        Maximum nesting depth of the structure being decoded. The value must
 *                          be greater than 0, and less than or equal to 2.147.483.647
 *
 * @return array|stdClass Returns the value encoded in json as an appropriate PHP type; unquoted
 *                        values true, false and null are returned as true, false and null
 *                        respectively
 *
 * @throws InvalidArgumentException if the json cannot be decoded or if the encoded data is
 *                                  deeper than the nesting limit
 */
Jay\Json::fromFile(string $path, bool $associative = false, int $depth = 512): array|stdClass;


/**
 * @param string|Stringable $contents The json string being decoded; this function only works
 *                                    with UTF-8 encoded strings
 * @param bool $associative           When true, JSON objects will be returned as associative
 *                                    arrays; when false, JSON objects will be returned as an
 *                                    instance of stdClass
 * @param int $depth                  Maximum nesting depth of the structure being decoded. The
 *                                    value must be greater than 0, and less than or equal to
 *                                    2.147.483.647
 *
 * @return array|stdClass Returns the value encoded in json as an appropriate PHP type; unquoted
 *                        values true, false and null are returned as true, false and null
 *                        respectively
 *
 * @throws InvalidArgumentException if the json cannot be decoded or if the encoded data is
 *                                  deeper than the nesting limit.
 */
Jay\Json::fromString(
  string|Stringable $contents,
  bool $associative = false,
  int $depth = 512
): array|stdClass;
```

## License

This library is licensed under the [MIT License](LICENSE).
