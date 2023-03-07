<?php
declare(strict_types = 1);

namespace Jay;

use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Scale\DigitalStorage\Gibibytes;
use ValueError;

class Json {
  public static function fromFile(string $path, bool $associative = false, int $depth = 512): array {
    if (is_readable($path) === false) {
      throw new InvalidArgumentException("File not found '{$path}'");
    }

    $json = file_get_contents($path);
    if ($json === false) {
      throw new InvalidArgumentException("Failed to read file contents '{$path}'");
    }

    return self::fromString($json, $associative, $depth);
  }

  public static function fromString(string $json, bool $associative = false, int $depth = 512): array {
    try {
      if (
        extension_loaded('simdjson') &&
        function_exists('simdjson_decode') &&
        strlen($json) < 4 * Gibibytes::IN_BYTES
      ) {
        // The maximum string length that can be passed to simdjson_decode() is 4GiB (4294967295 bytes).
        // json_decode() can decode longer strings.
        return simdjson_decode($json, $associative, $depth);
      }

      return json_decode($json, $associative, $depth, JSON_THROW_ON_ERROR);
    } catch (JsonException | RuntimeException | ValueError $exception) {
      // SimdJsonException extends RuntimeException
      // SimdJsonValueError extends ValueError
      throw new InvalidArgumentException($exception->getMessage(), $excepton->getCode(), $exception);
    }
  }
}
