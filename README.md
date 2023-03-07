# Jay

Jay is a thin wrapper for json & simdjson, allowing the fastest available json decoder to be used in a transparent way.

Under the hood, Jay will pick [simdjson](https://github.com/crazyxman/simdjson_php) if available and the json
encoded string is up to 4GiB (4294967295 bytes), otherwise it will fallback to PHP's core extension.

## Installation

To use Jay, simple run:

```bash
composer require flavioheleno/scale
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

## License

This library is licensed under the [MIT License](LICENSE).
