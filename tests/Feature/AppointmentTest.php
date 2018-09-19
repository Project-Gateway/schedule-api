<?php

namespace Tests\Feature;

use Helmich\JsonAssert\JsonAssertions;
use Mayconbordin\L5Fixtures\FixturesFacade as Fixtures;
use Tests\TestCase;

class AppointmentTest extends TestCase
{

    use JsonAssertions;

    public function setUp()
    {
        parent::setUp();
        Fixtures::setUp('tests/Fixtures');
        Fixtures::down();
        Fixtures::up();
    }

    /**
     *
     * @return void
     */
    public function testServicesByDate()
    {
        $response = $this->get('/appointments/2018-09-20');
        $response->assertStatus(200);

        $content = json_decode($response->content());

        dd($content);

    }

    /**
     * @group debug
     */
    public function testAvailability()
    {
        $response = $this->get('/availability/2018-09-20/1');
        $response->assertStatus(200);

        $content = json_decode($response->content());

        dd($content);
    }

}
