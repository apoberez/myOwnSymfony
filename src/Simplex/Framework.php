<?php
/**
 * Created by Alexander Pobereznichenko.
 * Date: 08.11.15
 * Time: 22:40
 */

namespace Simplex;


use Simplex\Events\FrameworkEvents;
use Simplex\Events\ResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class Framework
{
    /**
     * @var UrlMatcherInterface
     */
    private $matcher;

    /**
     * @var ControllerResolverInterface
     */
    private $resolver;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Framework constructor.
     * @param UrlMatcherInterface $matcher
     * @param ControllerResolverInterface $resolver
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        UrlMatcherInterface $matcher,
        ControllerResolverInterface $resolver,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     * @return mixed|Response
     */
    public function handle(Request $request)
    {
        $this->matcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->resolver->getController($request);
            $arguments = $this->resolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = new Response(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            $response = new Response(
                Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        };

        $this->dispatcher->dispatch(FrameworkEvents::ON_RESPONSE, new ResponseEvent($request, $response));

        return $response;
    }
}