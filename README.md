# Jay

Jay is a thin wrapper for json & simdjson, allowing the fastest available json decoder to be used in a transparent way.

Under the hood, **Jay** will pick [simdjson](https://github.com/simdjson/simdjson) if available and the json encoded
string is up to 4GiB (4.294.967.295 bytes), otherwise it will fallback to PHP's JSON Core Extension.

## Extension support

This library should work with either option below:

* [crazyxman/simdjson_php](https://github.com/crazyxman/simdjson_php) - Read only support
* [JakubOnderka/simdjson_php](https://github.com/JakubOnderka/simdjson_php) - Read and write support

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
$phpArray = json_decode($jsonEncoded, true);

// after
$phpArray = Jay\Json::fromString($jsonEncoded, true);
```

```php
$phpArray = ['a' => 'b', 'c' => true, 'd' => 10];

// before
$jsonEncoded = json_encode($phpArray);

// after
$jsonEncoded = Jay\Json::toString($phpArray);
```

```php
// before
$jsonEncoded = file_get_contents('path/to/file.json');
$phpArray = json_decode($jsonEncoded, true);

// after
$phpArray = Jay\Json::fromFile('path/to/file.json', true);
```

```php
$phpArray = ['a' => 'b', 'c' => true, 'd' => 10];

// before
$jsonEncoded = json_encode($phpArray);
file_put_contents('path/to/file.json', $jsonEncoded);

// after
Jay\Json::toFile('path/to/file.json', $phpArray);
```

## API

```php
/**
 * @param string $filename  Name of the file to read.
 * @param bool $associative When true, JSON objects will be returned as associative arrays; when false, JSON objects
 *                          will be returned as an instance of stdClass
 * @param int $depth        Maximum nesting depth of the structure being decoded. The value must be greater than 0,
 *                          and less than or equal to 2.147.483.647
 *
 * @return array|stdClass Returns the value encoded in JSON as an appropriate PHP type; unquoted values true, false
 *                        and null are returned as true, false and null respectively
 *
 * @throws InvalidArgumentException If the $filename argument is not a file, is not readable, if the JSON cannot be
 *                                  decoded or if the encoded data is deeper than the nesting limit.
 * @throws RuntimeException         If the contents of $filename cannot be read.
 */
Jay\Json::fromFile(string $filename, bool $associative = false, int $depth = 512): array|stdClass;

/**
 * @param string $filename Name of the file to write.
 * @param mixed $value     The value being encoded. Can be any type except a resource. All string data must be UTF-8
 *                         encoded.
 * @param int $flags       Bitmask consisting of JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP,
 *                         JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK,
 *                         JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT,
 *                         JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE,
 *                         JSON_THROW_ON_ERROR.
 * @param int $depth       Set the maximum depth. Must be greater than zero.
 *
 * @return int|false The number of bytes that were written to the file, or false on failure.
 *
 * @throws InvalidArgumentException If the $filename argument is not a writable file or if the JSON cannot be encoded
 *                                  or if the decoded data is deeper than the nesting limit.
 * @throws RuntimeException         If the encoded JSON cannot be write to $filename.
 */
Jay\Json::toFile(string $filename, mixed $value, int $flags = 0, int $depth = 512): int|false;

/**
 * @param string|Stringable $contents The JSON string being decoded; this function only works with UTF-8 encoded
 *                                    strings.
 * @param bool $associative           When true, JSON objects will be returned as associative arrays; when false,
 *                                    JSON objects will be returned as an instance of stdClass
 * @param int $depth                  Maximum nesting depth of the structure being decoded. The value must be greater
 *                                    than 0, and less than or equal to 2.147.483.647
 *
 * @return array|stdClass Returns the value encoded in JSON as an appropriate PHP type; unquoted values true, false
 *                        and null are returned as true, false and null respectively
 *
 * @throws InvalidArgumentException If the JSON cannot be decoded or if the encoded data is deeper than the nesting
 *                                  limit.
 */
Jay\Json::fromString(
  string|Stringable $contents,
  bool $associative = false,
  int $depth = 512
): array|stdClass;

/**
 * @param mixed $value The value being encoded. Can be any type except a resource. All string data must be UTF-8
 *                     encoded.
 * @param int $flags   Bitmask consisting of JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP,
 *                     JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK,
 *                     JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT,
 *                     JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE,
 *                     JSON_THROW_ON_ERROR.
 * @param int $depth   Set the maximum depth. Must be greater than zero.
 *
 * @return string A JSON encoded string.
 *
 * @throws InvalidArgumentException If the JSON cannot be encoded or if the decoded data is deeper than the nesting
 *                                  limit.
 */
Jay\Json::toString(mixed $value, int $flags = 0, int $depth = 512): string;
```

## License

This library is licensed under the [MIT License](LICENSE).
