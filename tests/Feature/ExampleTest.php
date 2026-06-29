<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    function test_the_application_root_redirects_to_admin()
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }
}
