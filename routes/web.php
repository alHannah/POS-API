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

                $router->post('/brand_dropdown',             'v1\web\dropdowns\StoreDropdownController@brand_dropdown');
                $router->post('/area_dropdown_create',       'v1\web\dropdowns\StoreDropdownController@area_dropdown_create');
                $router->post('/area_dropdown_get',          'v1\web\dropdowns\StoreDropdownController@area_dropdown_get');
                $router->post('/area_dropdown_stores_get',   'v1\web\dropdowns\StoreDropdownController@area_dropdown_stores_get');
                $router->post('/store_group_dropdown_create','v1\web\dropdowns\StoreDropdownController@store_group_dropdown_create');
                $router->post('/store_group_dropdown_get',   'v1\web\dropdowns\StoreDropdownController@store_group_dropdown_get');
                $router->post('/price_tier_dropdown',        'v1\web\dropdowns\StoreDropdownController@price_tier_dropdown');
                $router->post('/manager_dropdown',           'v1\web\dropdowns\StoreDropdownController@manager_dropdown');
                $router->post('/add_product_dropdown',       'v1\web\dropdowns\StoreDropdownController@add_product_dropdown');
                $router->post('/stores_dropdown',            'v1\web\dropdowns\StoreDropdownController@price_tier_dropdown');
            });

            $router->group(["prefix" => "/area", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',              'v1\web\stores\AreaController@create_update');
                $router->post('/archive_activate',           'v1\web\stores\AreaController@archive_activate');
                $router->post('/get',                        'v1\web\stores\AreaController@get');
            });

            $router->group(["prefix" => "/store_group", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {

                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',              'v1\web\stores\StoreGroupController@create_update');
                $router->post('/delete',                     'v1\web\stores\StoreGroupController@delete');
                $router->post('/get',                        'v1\web\stores\StoreGroupController@get');

            });

            $router->group(["prefix" => "/stores", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                    'v1\web\stores\StoresController@create_store');
                $router->post('/update',                    'v1\web\stores\StoresController@update_store');
                $router->post('/delete',                    'v1\web\stores\StoresController@archive_store_device');
                $router->post('/get',                       'v1\web\stores\StoresController@get_stores_devices');
                $router->get('/showProduct/{id}/{status}',           'v1\web\stores\StoresController@show_product');
                $router->get('/addProduct/{id}',            'v1\web\stores\StoresController@add_product');
                $router->post('/saveProduct',               'v1\web\stores\StoresController@save_product');
                $router->post('/activateProduct',           'v1\web\stores\StoresController@activate_product');
                $router->get('/editStore/{id}',             'v1\web\stores\StoresController@edit_store');
                $router->post('/dropdown',                  'v1\web\dropdowns\DropdownController@store_group_dropdown');
            });

            $router->group(["prefix" => "/schedule_group", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                     'v1\web\stores\ScheduleGroupController@create');
                $router->post('/update',                     'v1\web\stores\ScheduleGroupController@update');
                $router->post('/edit',                       'v1\web\stores\ScheduleGroupController@edit');
                $router->post('/delete',                     'v1\web\stores\ScheduleGroupController@delete');
                $router->get('/get',                         'v1\web\stores\ScheduleGroupController@get');
            });

            $router->group(["prefix" => "/store_hours", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                     'v1\web\stores\StoreHoursController@create');
                $router->post('/update',                     'v1\web\stores\StoreHoursController@update');
                $router->post('/delete',                     'v1\web\stores\StoreHoursController@delete');
                $router->post('/searchStoreHours',           'v1\web\stores\StoreHoursController@searchStoreHours');
                $router->post('/filterStoreHours',           'v1\web\stores\StoreHoursController@filterStoreHours');
                $router->post('/displayStoreHours',          'v1\web\stores\StoreHoursController@displayStoreHours');
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

            $router->group(["prefix" => "/drop_down", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/uom_category_dropdown',             'v1\web\dropdowns\ProductDropdownController@uom_category_dropdown');
                $router->post('/store_dropdown',                    'v1\web\dropdowns\ProductDropdownController@store_dropdown');
                $router->post('/product_store_dropdown',            'v1\web\dropdowns\ProductDropdownController@product_dropdown_per_store');
                $router->post('/uom_per_product',                   'v1\web\dropdowns\ProductDropdownController@uom_per_product');
                $router->post('/category_dropdown',                 'v1\web\dropdowns\ProductDropdownController@category_dropdown');
                $router->post('/product_s_dropdown',                'v1\web\dropdowns\ProductDropdownController@product_s_dropdown');
                $router->post('/product_w_dropdown',                'v1\web\dropdowns\ProductDropdownController@product_w_dropdown');
                $router->post('/for_packaging_dropdown',            'v1\web\dropdowns\ProductDropdownController@for_packaging_dropdown');
                $router->post('/brand_dropdown',                       'v1\web\dropdowns\ProductDropdownController@brand_dropdown');
                $router->post('/mop_dropdown',                         'v1\web\dropdowns\ProductDropdownController@mop_dropdown');
            });

            $router->group(["prefix" => "/product_list", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',                     'v1\web\products\ProductListController@create_update');
                $router->post('/get',                               'v1\web\products\ProductListController@get');
                $router->post('/archive_activate',                  'v1\web\products\ProductListController@archive_activate');
            });

            $router->group(["prefix" => "/price_tier", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/displayPriceTier',          'v1\web\products\PriceTierController@displayPriceTier');
                $router->post('/create',                    'v1\web\products\PriceTierController@create');
                $router->post('/displayTierProduct',        'v1\web\products\PriceTierController@displayTierProduct');
                $router->post('/update',                    'v1\web\products\PriceTierController@update');
                $router->post('/archivePriceTier',          'v1\web\products\PriceTierController@archivePriceTier');
                $router->post('/displayDetails',            'v1\web\products\PriceTierController@displayDetails');
            });

            $router->group(["prefix" => "/bom_and_packaging", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                            'v1\web\products\BillOfMaterialController@create');
                $router->post('/view',                              'v1\web\products\BillOfMaterialController@view');
                $router->post('/update',                            'v1\web\products\BillOfMaterialController@update');
                $router->post('/delete',                            'v1\web\products\BillOfMaterialController@delete');
                $router->post('/get',                                'v1\web\products\BillOfMaterialController@get');
                //$router->post('/remove_packaging',                  'v1\web\products\BillOfMaterialController@remove_packaging');
            });

            $router->group(["prefix" => "/uom_maintenance", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                             'v1\web\products\UomController@create');
                $router->post('/edit',                               'v1\web\products\UomController@edit');
                $router->post('/update',                             'v1\web\products\UomController@update');
                $router->post('/delete',                             'v1\web\products\UomController@delete');
                $router->get('/get',                                 'v1\web\products\UomController@get');
                $router->post('/createCategory',                     'v1\web\products\UomController@createCategory');
                $router->post('/editCategory',                       'v1\web\products\UomController@editCategory');
                $router->post('/updateCategory',                     'v1\web\products\UomController@updateCategory');
                $router->post('/deleteCategory',                     'v1\web\products\UomController@deleteCategory');
                $router->get('/getCategory',                         'v1\web\products\UomController@getCategory');
            });

            $router->group(["prefix" => "/product_classification", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                            'v1\web\products\ProductClassificationController@create_classification');
                $router->post('/update',                            'v1\web\products\ProductClassificationController@edit_classification');
                $router->post('/delete',                            'v1\web\products\ProductClassificationController@archive_classification');
                $router->post('/get',                               'v1\web\products\ProductClassificationController@get_classification');

            });

            $router->group(["prefix" => "/inventory_category", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create_update',                     'v1\web\products\InventoryCategoryController@create_update');
                $router->post('/get',                               'v1\web\products\InventoryCategoryController@get');
                $router->post('/archive_activate',                  'v1\web\products\InventoryCategoryController@archive_activate');
            });

            $router->group(["prefix" => "/pos_category", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/displayCategory',                   'v1\web\products\PosCategoryController@displayCategory');
                $router->post('/create',                            'v1\web\products\PosCategoryController@create');
                $router->post('/update',                            'v1\web\products\PosCategoryController@update');
                $router->post('/archiveCategory',                   'v1\web\products\PosCategoryController@archiveCategory');
            });

            $router->group(["prefix" => "/discounts", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/displayDiscount',                   'v1\web\products\DiscountController@displayDiscount');
                $router->post('/create',                            'v1\web\products\DiscountController@create');
                $router->post('/update',                            'v1\web\products\DiscountController@update');
                $router->post('/archiveDiscount',                   'v1\web\products\DiscountController@archiveDiscount');
            });

            $router->group(["prefix" => "/mode_of_payment", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/displayPriceTier',          'v1\web\products\PriceTierController@displayPriceTier');
                $router->post('/create',                    'v1\web\products\DiscountController@create');
                $router->post('/update',                    'v1\web\products\DiscountController@update');
                $router->post('/archiveDiscount',           'v1\web\products\DiscountController@archiveDiscount');
            });

            $router->group(["prefix" => "/order_type", "middleware" => "auth"], function () use ($router) {
                $router->get('/', function () use ($router) {
                    return view('index', ['api' => env('APP_NAME')]);
                });

                $router->post('/create',                            'v1\web\products\OrderTypeController@create_type');
                $router->post('/update',                            'v1\web\products\OrderTypeController@edit_type');
                $router->post('/delete',                            'v1\web\products\OrderTypeController@archive_type');
                $router->post('/get',                               'v1\web\products\OrderTypeController@get_type');
                $router->post('/set',                               'v1\web\products\OrderTypeController@set_default');

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
