<?php
declare(strict_types = 1);

// namespace *must* be "Jay" to force PHP's function resolver to grab the stub function version over global one
namespace Jay {
  // stub version of is_readable
   function is_readable(string $filename): bool {
    return false;
  }
}
