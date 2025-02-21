<?php
declare(strict_types = 1);

// namespace *must* be "Jay" to force PHP's function resolver to grab the stub function version over global one
namespace Jay {
  // PHP Warning:  "resource" is not a supported builtin type and will be interpreted as a class name.
  // Write "\Jay\resource" or import the class with "use" to suppress this warning.
  use resource;

  // stub version of is_writable
   function is_writable(string $filename): bool {
    return true;
  }

  // stub version of file_put_contents
  function file_put_contents(
    string $filename,
    mixed $data,
    int $flags = 0,
    resource|null $context = null
  ): int|false {
    return false;
  }
}
