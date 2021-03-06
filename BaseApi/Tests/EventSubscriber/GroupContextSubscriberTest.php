<?php

namespace OpenOrchestra\BaseApi\Tests\EventSubscriber;

use OpenOrchestra\BaseApiBundle\Controller\Annotation\Groups;
use OpenOrchestra\BaseApi\EventSubscriber\GroupContextSubscriber;
use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Test GroupContextSubscriberTest
 */
class GroupContextSubscriberTest extends AbstractBaseTestCase
{
    /**
     * @var GroupContextSubscriber
     */
    protected $subscriber;

    protected $event;
    protected $request;
    protected $resolver;
    protected $groupContext;
    protected $annotationReader;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->resolver = Phake::mock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');
        $this->annotationReader = Phake::mock('Doctrine\Common\Annotations\Reader');
        Phake::when($this->resolver)->getController(Phake::anyParameters())->thenReturn(array('\DateTime', 'add'));

        $this->request = Phake::mock('Symfony\Component\HttpFoundation\Request');
        Phake::when($this->request)->get('_route')->thenReturn('open_orchestra_api');

        $this->event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        Phake::when($this->event)->getRequest()->thenReturn($this->request);
        Phake::when($this->event)->isMasterRequest()->thenReturn(true);

        $this->groupContext = Phake::mock('OpenOrchestra\BaseApi\Context\GroupContext');

        $this->subscriber = new GroupContextSubscriber($this->groupContext, $this->annotationReader, $this->resolver);
    }

    /**
     * Test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->subscriber);
    }

    /**
     * Test subscribed event
     */
    public function testSubscribedEvent()
    {
        $this->assertArrayHasKey(KernelEvents::REQUEST, $this->subscriber->getSubscribedEvents());
        $this->assertTrue(method_exists($this->subscriber, 'onKernelRequest'));
    }

    /**
     * @param $masterRequest
     * @param $annotationPresent
     * @dataProvider provideKernelRequestData
     */
    public function testOnKernelRequestWithNoInteraction($masterRequest, $annotationPresent)
    {
        Phake::when($this->event)->isMasterRequest()->thenReturn($masterRequest);
        Phake::when($this->annotationReader)->getMethodAnnotation(Phake::anyParameters())->thenReturn($annotationPresent);

        $this->assertNull($this->subscriber->onKernelRequest($this->event));
        Phake::verify($this->groupContext, Phake::never())->setGroups(Phake::anyParameters());
    }

    /**
     * @return array
     */
    public function provideKernelRequestData()
    {
        return array(
            array(true, false),
            array(false, false),
            array(false, true),
        );
    }

    public function testOnKerneRequestWithGroupFound()
    {
        $groupArray = array('group_1');

        $groups = new Groups(array('value' => $groupArray ));
        Phake::when($this->annotationReader)->getMethodAnnotation(Phake::anyParameters())->thenReturn($groups);

        $this->subscriber->onKernelRequest($this->event);

        Phake::verify($this->groupContext)->setGroups($groupArray);
    }
}
