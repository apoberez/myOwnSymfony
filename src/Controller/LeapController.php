<?php
/**
 * Created by Alexander Pobereznichenko.
 * Date: 08.11.15
 * Time: 22:03
 */

namespace Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LeapController
{
    public function indexAction(Request $request)
    {
        if ($this->isLeapYear($request->attributes->get('year'))) {
            return new Response('Yes its leap year');
        } else {
            return new Response('No it\'s not leap year');
        }
    }

    private function isLeapYear($year = null)
    {
        if (null === $year) {
            $year = date('Y');
        }

        return 0 == $year % 400 || (0 == $year % 4 && 0 != $year % 100);
    }
}