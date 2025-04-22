<?php
declare(strict_types = 1);

// namespace *must* be "Jay" to force PHP's function resolver to grab the stub function version over global one
namespace Jay {
  // stub version of is_file
   function is_file(string $filename): bool {
    return true;
  }
}
