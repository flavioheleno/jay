<?php
declare(strict_types = 1);

namespace Jay;

use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Stringable;
use ValueError;
use stdClass;

/**
 * @link https://github.com/crazyxman/simdjson_php
 * @link https://github.com/JakubOnderka/simdjson_php
 */
class Json {
  /**
   * The maximum string length that can be passed to simdjson_decode()
   */
  private const MAX_BYTES = 4294967295;
  /**
   * @param string $path      Name of the file to read.
   * @param bool $associative When true, JSON objects will be returned as associative arrays; when false, JSON objects
   *                          will be returned as an instance of stdClass
   * @param int $depth        Maximum nesting depth of the structure being decoded. The value must be greater than 0,
   *                          and less than or equal to 2.147.483.647
   *
   * @return array|stdClass Returns the value encoded in JSON as an appropriate PHP type; unquoted values true, false
   *                        and null are returned as true, false and null respectively
   *
   * @throws InvalidArgumentException If the $path argument is not a readable file or if the JSON cannot be decoded or
   *                                  if the encoded data is deeper than the nesting limit.
   * @throws RuntimeException         If the contents of $path cannot be read.
   */
  public static function fromFile(string $path, bool $associative = false, int $depth = 512): array|stdClass {
    if (is_readable($path) === false) {
      throw new InvalidArgumentException("File \"{$path}\" not found");
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
      throw new RuntimeException("Failed to read file contents of \"{$path}\"");
    }

    return self::fromString($contents, $associative, $depth);
  }

  /**
   * @param string $path Name of the file to write.
   * @param mixed $value The value being encoded. Can be any type except a resource. All string data must be UTF-8
   *                     encoded.
   * @param int $flags   Bitmask consisting of JSON_FORCE_OBJECT, JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP,
   *                     JSON_HEX_APOS, JSON_INVALID_UTF8_IGNORE, JSON_INVALID_UTF8_SUBSTITUTE, JSON_NUMERIC_CHECK,
   *                     JSON_PARTIAL_OUTPUT_ON_ERROR, JSON_PRESERVE_ZERO_FRACTION, JSON_PRETTY_PRINT,
   *                     JSON_UNESCAPED_LINE_TERMINATORS, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE,
   *                     JSON_THROW_ON_ERROR.
   * @param int $depth   Set the maximum depth. Must be greater than zero.
   *
   * @return int|false The number of bytes that were written to the file, or false on failure.
   *
   * @throws InvalidArgumentException If the $path argument is not a writable file or if the JSON cannot be encoded or
   *                                  if the decoded data is deeper than the nesting limit.
   * @throws RuntimeException         If the encoded JSON cannot be write to $path.
   */
  public static function toFile(string $path, mixed $value, int $flags = 0, int $depth = 512): int|false {
    if (is_writable($path) === false) {
      throw new InvalidArgumentException("File \"{$path}\" is not writable");
    }

    $bytes = file_put_contents($path, self::toString($value, $flags, $depth), LOCK_EX);
    if ($bytes === false) {
      throw new RuntimeException("Failed to write JSON contents to \"{$path}\"");
    }

    return $bytes;
  }

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
        extension_loaded('simdjson') === true &&
        function_exists('simdjson_decode') === true &&
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
  public static function toString(mixed $value, int $flags = 0, int $depth = 512): string {
    try {
      if (
        extension_loaded('simdjson') === true &&
        function_exists('simdjson_encode') === true
      ) {
        $mappedFlags = 0;
        if ($flags & JSON_PRETTY_PRINT) {
          $mappedFlags |= SIMDJSON_PRETTY_PRINT;
        }

        if ($flags & JSON_INVALID_UTF8_SUBSTITUTE) {
          $mappedFlags |= SIMDJSON_INVALID_UTF8_SUBSTITUTE;
        }

        if ($flags & JSON_INVALID_UTF8_IGNORE) {
          $mappedFlags |= SIMDJSON_INVALID_UTF8_IGNORE;
        }

        return simdjson_encode($value, $mappedFlags);
      }

      return json_encode($value, $flags | JSON_THROW_ON_ERROR, $depth);
    } catch (JsonException | RuntimeException | ValueError $exception) {
      // SimdJsonEncoderException extends SimdJsonException
      // SimdJsonException extends RuntimeException
      throw new InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
    }

  }
}
