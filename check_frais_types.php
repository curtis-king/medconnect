<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$container = $app->make(Illuminate\Contracts\Container\Container::class);
Illuminate\Support\Facades\Facade::setFacadeApplication($container);

$types = \Illuminate\Support\Facades\DB::table('frais')->distinct()->pluck('type')->toArray();
echo json_encode($types, JSON_PRETTY_PRINT);
