<?php

namespace WPTS\Tests\Integration;

class ExampleTest extends IntegrationTest
{
    public function test_post_title_was_added()
    {
        $post_id = $this->factory()->post->create([
            'post_title' => 'Example post title',
        ]);

        $post = \get_post($post_id);

        $this->assertSame('Example post title', $post->post_title);
    }
}
