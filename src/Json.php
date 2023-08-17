<?php
declare(strict_types = 1);

namespace Jay;

use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Stringable;
use ValueError;
use stdClass;

class Json {
  /**
   * The maximum string length that can be passed to simdjson_decode()
   */
  private const MAX_BYTES = 4294967295;
  /**
   * @param string $path      Name of the file to read
   * @param bool $associative When true, JSON objects will be returned as associative arrays; when false,
   *                          JSON objects will be returned as an instance of stdClass
   * @param int $depth        Maximum nesting depth of the structure being decoded. The value must be
   *                          greater than 0, and less than or equal to 2.147.483.647
   *
   * @return array|stdClass Returns the value encoded in json as an appropriate PHP type; unquoted values
   *                        true, false and null are returned as true, false and null respectively
   *
   * @throws InvalidArgumentException if the json cannot be decoded or if the encoded data is deeper than
   *                                  the nesting limit.
   */
  public static function fromFile(string $path, bool $associative = false, int $depth = 512): array|stdClass {
    if (is_readable($path) === false) {
      throw new InvalidArgumentException("File \"{$path}\" not found");
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
      throw new InvalidArgumentException("Failed to read file contents of \"{$path}\"");
    }

    return self::fromString($contents, $associative, $depth);
  }

  /**
   * @param string|Stringable $contents The json string being decoded; this function only works with UTF-8
   *                                    encoded strings
   * @param bool $associative           When true, JSON objects will be returned as associative arrays;
   *                                    when false, JSON objects will be returned as an instance of stdClass
   * @param int $depth                  Maximum nesting depth of the structure being decoded. The value must
   *                                    be greater than 0, and less than or equal to 2.147.483.647
   *
   * @return array|stdClass Returns the value encoded in json as an appropriate PHP type; unquoted values
   *                        true, false and null are returned as true, false and null respectively
   *
   * @throws InvalidArgumentException if the json cannot be decoded or if the encoded data is deeper than
   *                                  the nesting limit.
   */
  public static function fromString(
    string|Stringable $contents,
    bool $associative = false,
    int $depth = 512
  ): array|stdClass {
    try {
      if ($contents instanceof Stringable) {
        $contents = (string)$contents;
      }
      if (
        extension_loaded('simdjson') &&
        function_exists('simdjson_decode') &&
        strlen($contents) <= self::MAX_BYTES
      ) {
        // The maximum string length that can be passed to simdjson_decode() is 4GiB (4294967295 bytes).
        // json_decode() can decode longer strings.
        return simdjson_decode($contents, $associative, $depth);
      }

      return json_decode($contents, $associative, $depth, JSON_THROW_ON_ERROR);
    } catch (JsonException | RuntimeException | ValueError $exception) {
      // SimdJsonException extends RuntimeException
      // SimdJsonValueError extends ValueError
      throw new InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
    }
  }
}
