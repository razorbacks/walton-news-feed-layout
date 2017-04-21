<?php

use razorbacks\walton\news\Publication;

class PublicationTest extends PHPUnit_Framework_TestCase
{
    public function test_creates_publication()
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

    /**
     * @expectedException InvalidArgumentException
     */
    public function test_rejects_invalid_view()
    {
        $parameters = array(
            'categories' => array(1),
            'count' => 1,
            'view' => 'some non-existent view',
            'comments' => 'something',
        );

        $publication = new Publication($parameters, 1);
    }

    public function test_gets_and_sets_command()
    {
        $parameters = array(
            'categories' => array(1),
            'count' => 1,
            'view' => 'list',
            'comments' => 'something',
        );
        $publication = new Publication($parameters, 1);

        $this->assertInstanceOf('\razorbacks\walton\news\Publication', $publication);
        $this->assertTrue($publication->valid);

        $command = explode(' ', $publication->getCommand());
        $publication->setCommand(implode(' ', $command));

        $this->assertTrue($publication->valid);
    }

    public function test_invalidates_missing_query_string()
    {
        $parameters = array(
            'categories' => array(1),
            'count' => 1,
            'view' => 'list',
            'comments' => 'something',
        );
        $publication = new Publication($parameters, 1);

        $this->assertInstanceOf('\razorbacks\walton\news\Publication', $publication);
        $this->assertTrue($publication->valid);

        $command = explode(' ', $publication->getCommand());
        unset($command[2]);
        $publication->setCommand(implode(' ', $command));

        $this->assertFalse($publication->valid);
    }

    public function test_invalidates_missing_categories_in_query_string()
    {
        $parameters = array(
            'categories' => array(1),
            'count' => 1,
            'view' => 'list',
            'comments' => 'something',
        );
        $publication = new Publication($parameters, 1);

        $this->assertInstanceOf('\razorbacks\walton\news\Publication', $publication);
        $this->assertTrue($publication->valid);

        $command = explode(' ', $publication->getCommand());
        $command[2] = str_replace('categories', '', $command[2]);
        $publication->setCommand(implode(' ', $command));

        $this->assertFalse($publication->valid);
    }
}
