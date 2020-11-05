<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use PHPUnit\Framework\TestCase;

class RussianFederalPhoneNumberTest extends TestCase
{
    /** @test */
    public function it_()
    {
        $number = '8 (950) 288-56-235';
        $dt = new RussianFederalPhoneNumber($number);
        print_r($dt->normalize()->getData());
    }
}