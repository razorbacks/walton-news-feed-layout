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

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCanRejectNonExistentView()
    {
        $parameters = array(
            'categories' => array(1),
            'count' => 1,
            'view' => 'some non-existent view',
            'comments' => 'something',
        );

        $publication = new Publication($parameters, 1);
    }

    public function testCanGetAndSetCommand()
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

    public function testCanInvalidateMissingQueryString()
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

    public function testCanInvalidateMissingCategoriesInQueryString()
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
