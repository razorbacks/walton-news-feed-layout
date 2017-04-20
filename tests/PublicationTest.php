<?php

use razorbacks\walton\news\Publication;

class PublicationTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreatePublication()
    {
        $parameters = array(
            'categories' => array(1),
            'count' => 1,
            'view' => 'list',
            'comments' => 'something',
        );

        $publication = new Publication($parameters, 1);

        $this->assertInstanceOf('\razorbacks\walton\news\Publication', $publication);
    }
}
