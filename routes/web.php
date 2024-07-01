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

            $router->group(["prefix" => "/web"], function () use ($router) {
                $router->post('/login', 'v1\web\LoginController@login');
                $router->post('/logout', 'v1\web\LoginController@logout');
            });

        });

          // ----------------------- AUDIT TRAIL --------------------------------------
          $router->group(["prefix" => "/audit_trail", "middleware" => "auth"], function () use ($router) {
            $router->get('/', function () use ($router) {
                return view('index', ['api' => env('APP_NAME')]);
            });

            $router->post('/',            'Controller@audit_trail');


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

            $router->group(["prefix" => "/drop_down", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/brand_dropdown',                'v1\web\dropdowns\DropdownController@brand_dropdown');
                $router->post('/store_group_dropdown',          'v1\web\dropdowns\DropdownController@store_group_dropdown');
                $router->post('/price_tier_dropdown',           'v1\web\dropdowns\DropdownController@price_tier_dropdown');
                $router->post('/manager_dropdown',              'v1\web\dropdowns\DropdownController@manager_dropdown');
                $router->post('/add_product_dropdown',          'v1\web\dropdowns\DropdownController@add_product_dropdown');
                $router->post('/stores_dropdown',               'v1\web\dropdowns\DropdownController@price_tier_dropdown');
            });

            $router->group(["prefix" => "/area", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',                 'v1\web\stores\AreaController@create_update');
                $router->post('/delete',                        'v1\web\stores\AreaController@delete');
                $router->post('/get',                           'v1\web\stores\AreaController@get');
            });

            $router->group(["prefix" => "/store_group", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {

                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',                 'v1\web\stores\StoreGroupController@create_update');
                $router->post('/delete',                        'v1\web\stores\StoreGroupController@delete');
                $router->post('/get',                           'v1\web\stores\StoreGroupController@get');

            });

            $router->group(["prefix" => "/stores", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                    'v1\web\stores\StoresController@create_store');
                $router->post('/update',                    'v1\web\stores\StoresController@update_store');
                $router->post('/delete',                    'v1\web\stores\StoresController@archive_store_device');
                $router->post('/get',                       'v1\web\stores\StoresController@get_stores_devices');
                $router->get('/showProduct/{id}',           'v1\web\stores\StoresController@show_product');
                $router->get('/addProduct/{id}',            'v1\web\stores\StoresController@add_product');
                $router->post('/saveProduct',               'v1\web\stores\StoresController@save_product');
                $router->post('/activateProduct',           'v1\web\stores\StoresController@activate_product');
                $router->get('/editStore/{id}',             'v1\web\stores\StoresController@edit_store');
                $router->post('/dropdown',                   'v1\web\dropdowns\DropdownController@store_group_dropdown');
            });

            $router->group(["prefix" => "/schedule_group", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                        'v1\web\stores\ScheduleGroupController@create');
                $router->post('/update',                        'v1\web\stores\ScheduleGroupController@update');
                $router->post('/edit',                          'v1\web\stores\ScheduleGroupController@edit');
                $router->post('/delete',                        'v1\web\stores\ScheduleGroupController@delete');
                $router->get('/get',                            'v1\web\stores\ScheduleGroupController@get');
            });

            $router->group(["prefix" => "/store_hours", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                     'v1\web\stores\StoreHoursController@create');
                $router->post('/update',                     'v1\web\stores\StoreHoursController@update');
                $router->post('/delete',                     'v1\web\stores\StoreHoursController@delete');
                $router->post('/searchStoreHours',          'v1\web\stores\StoreHoursController@searchStoreHours');
                $router->post('/filterStoreHours',           'v1\web\stores\StoreHoursController@filterStoreHours');
                $router->post('/getStoreHours',              'v1\web\stores\StoreHoursController@getStoreHours');
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
