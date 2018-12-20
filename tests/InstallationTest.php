<?php

namespace Xoco70\LaravelTournaments\Tests;

class InstallationTest extends TestCase
{
    /** @test */
    public function it_installs()
    {
        exec('tests/test_installation.sh', $output, $return_code);
        dd($output);
        self::assertEquals($return_code, 0);
    }
}
