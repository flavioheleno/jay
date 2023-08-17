<?php
declare(strict_types = 1);

namespace Jay {
  use RuntimeException;
  use resource;

  // mocked version of file_get_contents
  function file_get_contents(
    string $filename,
    bool $use_include_path = false,
    resource|null $context = null,
    int $offset = 0,
    int|null $length = null
  ): string|false {
    return false;
  }
};
