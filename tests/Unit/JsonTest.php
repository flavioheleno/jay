<?php
declare(strict_types = 1);

namespace Jay\Test\Unit;

use InvalidArgumentException;
use Jay\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresFunction;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stringable;

#[CoversClass(Json::class)]
class JsonTest extends TestCase {
  #[Group('json'), Group('simdjson')]
  #[RunInSeparateProcess]
  public function testFromFileWithUnreadablePath(): void {
    require_once(__DIR__ . '/../Stub/is_readable.php');

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('File "jay.json" not found');

    Json::fromFile('jay.json');
  }

  #[Group('json'), Group('simdjson')]
  #[RunInSeparateProcess]
  public function testFromFileWithFailToReadContents(): void {
    require_once(__DIR__ . '/../Stub/file_get_contents.php');

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Failed to read file contents of "jay.json"');

    Json::fromFile('jay.json');
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

    $tmpfname = tempnam(sys_get_temp_dir(), 'jay');
    file_put_contents($tmpfname, json_encode($obj));

    $decoded = Json::fromFile($tmpfname, true);
    $this->assertEquals($obj, $decoded);
  }

  #[Group('json'), Group('simdjson')]
  #[RunInSeparateProcess]
  public function testToFileWithUnwritablePath(): void {
    require_once(__DIR__ . '/../Stub/is_writable.php');

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('File "jay.json" is not writable');

    Json::toFile('jay.json', ['a' => 'b']);
  }

  #[Group('json'), Group('simdjson')]
  #[RunInSeparateProcess]
  public function testToFileWithFailToWriteContents(): void {
    require_once(__DIR__ . '/../Stub/file_put_contents.php');

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Failed to write JSON contents to "jay.json"');

    Json::toFile('jay.json', ['a' => 'b']);
  }

  #[Group('json'), Group('simdjson')]
  public function testToFile(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];

    $tmpfname = tempnam(sys_get_temp_dir(), 'jay');
    $bytes = Json::toFile($tmpfname, $obj);

    $this->assertSame(58, $bytes);
    $this->assertStringEqualsFile($tmpfname, '{"a":1,"b":"foo","c":true,"d":null,"e":[5,6],"f":{"g":[]}}');
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

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_encode')]
  public function testToStringWithInvalidContentSimdJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Type is not supported');

    Json::toString(tmpfile());
  }

  #[Group('simdjson')]
  #[RequiresPhpExtension('simdjson'), RequiresFunction('simdjson_encode')]
  public function testToStringWithSimdJson(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];
    $json = '{"a":1,"b":"foo","c":true,"d":null,"e":[5,6],"f":{"g":[]}}';

    $encoded = Json::toString($obj);
    $this->assertSame($json, $encoded);
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_encode')]
  public function testToStringWithInvalidContentCoreJson(): void {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Type is not supported');

    Json::toString(tmpfile());
  }

  #[Group('json')]
  #[RequiresPhpExtension('json'), RequiresFunction('json_encode')]
  public function testToStringWithCoreJson(): void {
    $obj = [
      'a' => 1,
      'b' => 'foo',
      'c' => true,
      'd' => null,
      'e' => [5, 6],
      'f' => ['g' => []]
    ];
    $json = '{"a":1,"b":"foo","c":true,"d":null,"e":[5,6],"f":{"g":[]}}';

    $encoded = Json::toString($obj);
    $this->assertSame($json, $encoded);
  }

}
