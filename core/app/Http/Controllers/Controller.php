<?php

namespace App\Http\Controllers;

use Laramin\Utility\Onumoti;

abstract class Controller
{
    public function __construct()
    {
        $className = get_called_class();
        Onumoti::mySite($this,$className);
    }

    public static function middleware()
    {
        return [];
    }

}
