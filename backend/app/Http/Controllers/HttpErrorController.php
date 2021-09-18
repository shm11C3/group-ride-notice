<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HttpErrorController extends Controller
{
    public function unauthorized()
    {
        return abort(401);
    }

    public function forbidden()
    {
        return abort(403);
    }

    public function notFound()
    {
        return abort(404);
    }

    public function methodNotAllowed()
    {
        return abort(405);
    }
}
