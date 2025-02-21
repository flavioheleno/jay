<?php
declare(strict_types = 1);

// namespace *must* be "Jay" to force PHP's function resolver to grab the stub function version over global one
namespace Jay {
  // PHP Warning:  "resource" is not a supported builtin type and will be interpreted as a class name.
  // Write "\Jay\resource" or import the class with "use" to suppress this warning.
  use resource;

  // stub version of is_readable
   function is_readable(string $filename): bool {
    return true;
  }

  // stub version of file_get_contents
  function file_get_contents(
    string $filename,
    bool $use_include_path = false,
    resource|null $context = null,
    int $offset = 0,
    int|null $length = null
  ): string|false {
    return false;
  }
}
