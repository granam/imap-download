<?php
namespace Granam\Tests\Mail\Download;

use Granam\Mail\Download\ToString;
use PHPUnit\Framework\TestCase;

class ToStringTest extends TestCase
{
    /**
     * @test
     */
    public function I_can_ensure_magic_to_string_method_by_this_interface()
    {
        $reflectionClass = new \ReflectionClass(ToString::class);
        self::assertTrue($reflectionClass->hasMethod('__toString'));
    }
}
