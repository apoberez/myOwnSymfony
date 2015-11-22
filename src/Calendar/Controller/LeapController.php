<?php

namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LeapController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if ($this->isLeapYear($request->attributes->get('year'))) {
            $response = new Response('Yes its leap year');
        } else {
            $response = new Response('No it\'s not leap year ' . rand(1, 1000) . ' ');
        }

        $response->setTtl(10);
        return $response;
    }

    /**
     * @param null $year
     * @return bool
     */
    private function isLeapYear($year = null)
    {
        if (null === $year) {
            $year = date('Y');
        }

        return 0 == $year % 400 || (0 == $year % 4 && 0 != $year % 100);
    }
}
