<?php

namespace Tests\Feature;

use Helmich\JsonAssert\JsonAssertions;
use Mayconbordin\L5Fixtures\FixturesFacade as Fixtures;
use Tests\TestCase;

class ClientTest extends TestCase
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
     * @return void
     */
    public function testUserIndex()
    {
        $response = $this->get('/clients');
        $response->assertStatus(200);

        $content = json_decode($response->content());

        $this->assertEquals(3, count($content));
        $this->assertJsonValueEquals($content, '$.0.first_name', 'Jose');
        $this->assertJsonValueEquals($content, '$.0.last_name', 'Guerrero');
        $this->assertJsonValueEquals($content, '$.0.phone', '713-941-2868');
        $this->assertJsonValueEquals($content, '$.0.email', 'al2013@hotmail.com');

    }

    /**
     *
     */
    public function testUserShow()
    {
        $response = $this->get('/clients/1');
        $response->assertStatus(200);

        $content = json_decode($response->content());

        $this->assertJsonValueEquals($content, '$.first_name', 'Jose');
        $this->assertJsonValueEquals($content, '$.last_name', 'Guerrero');
        $this->assertJsonValueEquals($content, '$.phone', '713-941-2868');
        $this->assertJsonValueEquals($content, '$.email', 'al2013@hotmail.com');
        $this->assertEquals(3, count($content->appointments));
        $this->assertJsonValueEquals($content, '$.appointments.0.client_id', 1);
        $this->assertJsonValueEquals($content, '$.appointments.0.provider_id', 1);
        $this->assertJsonValueEquals($content, '$.appointments.0.service_id', 1);
        $this->assertJsonValueEquals($content, '$.appointments.0.start_time', '2018-09-20 08:00:00');
        $this->assertJsonValueEquals($content, '$.appointments.0.finish_time', '2018-09-20 09:00:00');
        $this->assertJsonValueEquals($content, '$.appointments.1.client_id', 1);
        $this->assertJsonValueEquals($content, '$.appointments.1.provider_id', 2);
        $this->assertJsonValueEquals($content, '$.appointments.1.service_id', 2);
        $this->assertJsonValueEquals($content, '$.appointments.1.start_time', '2018-09-20 15:00:00');
        $this->assertJsonValueEquals($content, '$.appointments.1.finish_time', '2018-09-20 16:00:00');
        $this->assertJsonValueEquals($content, '$.appointments.2.client_id', 1);
        $this->assertJsonValueEquals($content, '$.appointments.2.provider_id', 3);
        $this->assertJsonValueEquals($content, '$.appointments.2.service_id', 5);
        $this->assertJsonValueEquals($content, '$.appointments.2.start_time', '2018-09-21 15:00:00');
        $this->assertJsonValueEquals($content, '$.appointments.2.finish_time', '2018-09-21 16:00:00');

    }

    /**
     *
     */
    public function testUserUpdate()
    {
        $response = $this->put('/clients/1', ['first_name' => 'John']);
        $response->assertStatus(200);

        $content = json_decode($response->content());

        $this->assertJsonValueEquals($content, '$.first_name', 'John');
        $this->assertJsonValueEquals($content, '$.last_name', 'Guerrero');
        $this->assertJsonValueEquals($content, '$.phone', '713-941-2868');
        $this->assertJsonValueEquals($content, '$.email', 'al2013@hotmail.com');

        $this->assertDatabaseHas('clients', ['id' => 1, 'first_name' => 'John']);
    }


}
