<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class SetDatabaseConnection
{
    public function handle(Request $request, Closure $next, $connection)
    {
        DB::setDefaultConnection($connection);
        Model::clearBootedModels();
        Schema::getConnection()->getConfig()['database'] = $connection;

        return $next($request);
    }
}
