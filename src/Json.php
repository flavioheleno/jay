<?php
declare(strict_types = 1);

namespace Jay;

use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Scale\DigitalStorage\Gibibytes;
use Stringable;
use ValueError;
use stdClass;

class Json {
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

  public static function fromString(
    string|Stringable $contents,
    bool $associative = false,
    int $depth = 512
  ): array|stdClass {
    try {
      if (
        extension_loaded('simdjson') &&
        function_exists('simdjson_decode') &&
        strlen($contents) <= 4 * Gibibytes::IN_BYTES
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
