<?php
declare(strict_types = 1);

namespace Jay\Test;

use InvalidArgumentException;
use Jay\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresFunction;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Stringable;

#[CoversClass(Json::class)]
class JsonTest extends TestCase {
  #[Group('json'), Group('simdjson')]
  public function testFromFileWithUnreadablePath(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('File "/file/not/found/jay.json" not found');

    Json::fromFile('/file/not/found/jay.json');
  }

  #[Group('json'), Group('simdjson')]
  #[RunInSeparateProcess]
  public function testFromFileWithFailToReadContents(): void {
    require_once(__DIR__ . '/mockfunctions.php');

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Failed to read file contents of "/dev/null"');

    Json::fromFile('/dev/null');
  }

  #[Group('json'), Group('simdjson')]
  public function testFromFile(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];

    $tmpfname = tempnam('/tmp', 'jay');
    file_put_contents($tmpfname, json_encode($obj));

    $decoded = Json::fromFile($tmpfname, true);
    $this->assertEquals($obj, $decoded);
  }

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_decode')]
  public function testFromStringWithEmptyStringContentSimdJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Empty: no JSON found');

    Json::fromString('');
  }

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_decode')]
  public function testFromStringWithInvalidStringContentSimdJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.');

    Json::fromString('{"a":1');
  }

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_decode')]
  public function testFromStringWithEmptyStringableContentSimdJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Empty: no JSON found');

    Json::fromString(
      new class () implements Stringable {
        public function __toString(): string {
          return '';
        }
      }
    );
  }

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_decode')]
  public function testFromStringWithInvalidStringableContentSimdJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('The JSON document has an improper structure: missing or superfluous commas, braces, missing keys, etc.');

    Json::fromString(
      new class () implements Stringable {
        public function __toString(): string {
          return '{"a":1';
        }
      }
    );
  }

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_decode')]
  public function testFromStringWithStringAndSimdJson(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];
    $json = '{"a":1,"b":"foo","c":true,"d":null,"e":[5,6],"f":{"g":[]}}';

    $decoded = Json::fromString($json, true);
    $this->assertEquals($obj, $decoded);
  }

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_decode')]
  public function testFromStringWithStringableAndSimdJson(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];
    $json = new class implements Stringable {
      public function __toString(): string {
        return '{"a":1,"b":"foo","c":true,"d":null,"e":[5,6],"f":{"g":[]}}';
      }
    };

    $decoded = Json::fromString($json, true);
    $this->assertEquals($obj, $decoded);
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_decode')]
  public function testFromStringWithEmptyStringContentCoreJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Syntax error');

    Json::fromString('');
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_decode')]
  public function testFromStringWithInvalidStringContentCoreJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Syntax error');

    Json::fromString('{"a":1');
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_decode')]
  public function testFromStringWithEmptyStringableContentCoreJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Syntax error');

    Json::fromString(
      new class () implements Stringable {
        public function __toString(): string {
          return '';
        }
      }
    );
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_decode')]
  public function testFromStringWithInvalidStringableContentCoreJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Syntax error');

    Json::fromString(
      new class () implements Stringable {
        public function __toString(): string {
          return '{"a":1';
        }
      }
    );
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_decode')]
  public function testFromStringWithStringAndCoreJson(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];
    $json = '{"a":1,"b":"foo","c":true,"d":null,"e":[5,6],"f":{"g":[]}}';

    $decoded = Json::fromString($json, true);
    $this->assertEquals($obj, $decoded);
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_decode')]
  public function testFromStringWithStringableAndCoreJson(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];
    $json = new class implements Stringable {
      public function __toString(): string {
        return '{"a":1,"b":"foo","c":true,"d":null,"e":[5,6],"f":{"g":[]}}';
      }
    };

    $decoded = Json::fromString($json, true);
    $this->assertEquals($obj, $decoded);
  }
}
