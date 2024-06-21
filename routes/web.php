
<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return view('index', ['api' => env('APP_NAME')]);
});


$router->group(["prefix" => "/api", 'middleware' => 'cors'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return view('index', ['api' => env('APP_NAME')]);
    });

    $router->group(["prefix" => "/v1"], function () use ($router) {
        $router->get('/', function () use ($router) {
            return view('index', ['api' => env('APP_NAME')]);
        });

        $router->group(["prefix" => "/account"], function () use ($router) {
            $router->post('/login', 'v1\LoginController@login');
            $router->post('/logout', 'v1\LoginController@logout');
        });


        // ----------------------- HOME --------------------------------------
        $router->group(["prefix" => "/home", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

            // $router->post('/get',            'v1\HomeController@index');


        });

          // ----------------------- STORE --------------------------------------
          $router->group(["prefix" => "/store", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

            $router->group(["prefix" => "/area", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',             'v1\web\stores\AreaController@create_update');
                $router->post('/delete',                    'v1\web\stores\AreaController@delete');
                $router->get('/get',                        'v1\web\stores\AreaController@get');

            });

            $router->group(["prefix" => "/store_group", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',             'v1\web\stores\AreaController@create_update');
                $router->post('/delete',                    'v1\web\stores\AreaController@delete');
                $router->get('/get',                        'v1\web\stores\AreaController@get');
                
            });


        });

        // ----------------------- ACCESS --------------------------------------
        $router->group(["prefix" => "/access", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- USER --------------------------------------
        $router->group(["prefix" => "/user", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });


        });

        // ----------------------- OIC --------------------------------------
        $router->group(["prefix" => "/oic", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });


        });

        // ----------------------- OIC2 --------------------------------------
        $router->group(["prefix" => "/oic2", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- SALES --------------------------------------
        $router->group(["prefix" => "/sales", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- DAILY SALES --------------------------------------
        $router->group(["prefix" => "/daily_sales", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- SALES REPORT  --------------------------------------
        $router->group(["prefix" => "/sales_report", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- Z-READINGS --------------------------------------
        $router->group(["prefix" => "/z-readings", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- X-READINGS --------------------------------------
        $router->group(["prefix" => "/x-readings", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- Y-READINGS --------------------------------------
        $router->group(["prefix" => "/y-readings", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- CASH --------------------------------------
        $router->group(["prefix" => "/cash", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- PURCHASE --------------------------------------
        $router->group(["prefix" => "/purchase", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- CUSTOMER --------------------------------------
        $router->group(["prefix" => "/customer", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- INVENTORY --------------------------------------
        $router->group(["prefix" => "/inventory", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- PRODUCT --------------------------------------
        $router->group(["prefix" => "/product", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- POS CATEGORY --------------------------------------
        $router->group(["prefix" => "/pos_category", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- DATA PURGE --------------------------------------
        $router->group(["prefix" => "/data-purge", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

        // ----------------------- DATA RETRIEVE --------------------------------------
        $router->group(["prefix" => "/data-retrieve", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

        });

    });
});
