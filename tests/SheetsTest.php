<?php

namespace Revolution\Google\Sheets\Tests;

use Mockery as m;
use Revolution\Google\Sheets\GoogleSheetClient;
use Revolution\Google\Sheets\Facades\Sheets;
use Revolution\Google\Sheets\Traits\GoogleSheets;

class SheetsTest extends TestCase
{
    /**
     * @var \Revolution\Google\Sheets\Providers\Client
     */
    protected $google;

    public function setUp(): void
    {
        parent::setUp();

        $this->google = m::mock(GoogleSheetClient::class);
        app()->instance(GoogleSheetClient::class, $this->google);
    }

    public function tearDown(): void
    {
        m::close();
    }

    public function testService()
    {
        $this->google->shouldReceive('make')->once()->andReturns(m::mock(\Google\Service\Sheets::class));

        //        Sheets::setService($this->google->make('Sheets'));

        $this->assertInstanceOf(\Google\Service\Sheets::class, Sheets::getService());
    }

    public function testSetAccessToken()
    {
        $this->google->shouldReceive('getCache')->once()->andReturn(m::self());
        $this->google->shouldReceive('clear')->once();
        $this->google->shouldReceive('setAccessToken')->once();
        $this->google->shouldReceive('isAccessTokenExpired')->once()->andReturns(true);
        $this->google->shouldReceive('fetchAccessTokenWithRefreshToken')->once();
        $this->google->shouldReceive('make')->times(2)->andReturns(
            m::mock(\Google\Service\Sheets::class),
            m::mock(\Google\Service\Drive::class)
        );

        $photos = Sheets::setAccessToken([
            'access_token'  => 'test',
            'refresh_token' => 'test',
            'expires_in'    => 0,
        ]);

        $this->assertInstanceOf(\Google\Service\Sheets::class, $photos->getService());
    }

    public function testTrait()
    {
        Sheets::shouldReceive('setAccessToken')->with('test')->once()->andReturn(m::self());

        $sheets = (new User())->sheets();

        $this->assertNotNull($sheets);
    }
}

class User
{
    use GoogleSheets;

    public function sheetsAccessToken()
    {
        return 'test';
    }
}
