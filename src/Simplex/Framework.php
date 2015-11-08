<?php
/**
 * Created by Alexander Pobereznichenko.
 * Date: 08.11.15
 * Time: 22:40
 */

namespace Simplex;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

class Framework
{
    /**
     * @var UrlMatcher
     */
    private $matcher;

    /**
     * @var ControllerResolver
     */
    private $resolver;

    /**
     * Framework constructor.
     */
    public function __construct(UrlMatcher $matcher, ControllerResolver $resolver)
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
    }

    public function handle(Request $request)
    {
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

        return $response;
    }
}