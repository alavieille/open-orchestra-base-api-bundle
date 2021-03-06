<?php

namespace OpenOrchestra\BaseApi\Tests\Context;

use OpenOrchestra\BaseApi\Context\GroupContext;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;

/**
 * Test GroupContextTest
 */
class GroupContextTest extends AbstractBaseTestCase
{
    /**
     * @var GroupContext
     */
    protected $context;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->context = new GroupContext();
    }

    /**
     * @param string $groupName
     *
     * @dataProvider provideGroupName
     */
    public function testContainsGroup($groupName)
    {
        $this->context->setGroups(array($groupName));

        $this->assertTrue($this->context->hasGroup($groupName));
        $this->assertFalse($this->context->hasGroup("foo"));

    }

    /**
     * @param string $groupName
     *
     * @dataProvider provideGroupName
     */
    public function testAddGroup($groupName)
    {
        $this->context->addGroup($groupName);

        $this->assertTrue($this->context->hasGroup($groupName));
        $this->assertFalse($this->context->hasGroup("foo"));
    }

    /**
     * @return array
     */
    public function provideGroupName()
    {
        return array(
            array('GROUP_1'),
            array('FOO_GROUP'),
        );
    }
}
