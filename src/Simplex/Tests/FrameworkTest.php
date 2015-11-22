<?php
/**
 * Created by Alexander Pobereznichenko.
 * Date: 11.11.15
 * Time: 22:44
 */

namespace Simplex\Tests;

use Simplex\Framework;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class FrameworkTest extends \PHPUnit_Framework_TestCase
{
    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());

        $response = $framework->handle(new Request());
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testErrorHandling()
    {
        $framework = $this->getFrameworkForException(new \RuntimeException());

        $response = $framework->handle(new Request());
        $this->assertSame(500, $response->getStatusCode());
    }

    public function testResponse()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|UrlMatcher $matcher */
        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->returnValue([
                '_route' => 'foo',
                'name' => 'Alexander',
                '_controller' => function ($name) {
                    return new Response('Hello ' . $name);
                }
            ]));
        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($this->getMock('Symfony\Component\Routing\RequestContext'));

        /** @var \PHPUnit_Framework_MockObject_MockObject|ControllerResolverInterface $resolver */
        $resolver = new ControllerResolver();

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $framework = new Framework($matcher, $resolver, $dispatcher);
        $response = $framework->handle(new Request());

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testEventsAreDispatched()
    {
        //todo
    }

    /**
     * @param $exception
     * @return Framework
     */
    private function getFrameworkForException(\Exception $exception)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|UrlMatcher $matcher */
        $matcher = $this->getMock('Symfony\Component\Routing\Matcher\UrlMatcherInterface');
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception));
        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->willReturn($this->getMock('Symfony\Component\Routing\RequestContext'));

        /** @var \PHPUnit_Framework_MockObject_MockObject|ControllerResolverInterface $resolver */
        $resolver = $this->getMock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        return new Framework($matcher, $resolver, $dispatcher);
    }
}
