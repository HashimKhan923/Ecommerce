<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//common routes start

Route::post('/login', '\App\Http\Controllers\AuthController@login');
Route::post('/forgetPassword', '\App\Http\Controllers\AuthController@forgetpassword');
Route::post('/checktoken', '\App\Http\Controllers\AuthController@token_check');
Route::post('/resetPassword', '\App\Http\Controllers\AuthController@reset_password');
Route::post('/change_password', '\App\Http\Controllers\AuthController@passwordChange');
Route::get('/verification/{id}', '\App\Http\Controllers\AuthController@verification');
Route::get('/logout/{id}', 'App\Http\Controllers\AuthController@logout');


// common routes ends

/// admin Register
Route::post('/admin/login', '\App\Http\Controllers\Admin\AuthController@login');
Route::post('/admin/register', 'App\Http\Controllers\Admin\AuthController@register');

/// seller Register
Route::post('/seller/login', '\App\Http\Controllers\Seller\AuthController@login');
Route::post('/seller/register', 'App\Http\Controllers\Seller\AuthController@register');

/// customer Register
Route::post('/login', '\App\Http\Controllers\Customer\AuthController@login');
Route::post('/customer/register', 'App\Http\Controllers\Customer\AuthController@register');


Route::group(['middleware' => ['auth:api']], function(){



        Route::middleware(['admin'])->group(function () {


            /////////////////////////////////// Admin Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

            Route::get('/admin/profile/view/{id}', 'App\Http\Controllers\Admin\AuthController@profile_view');
            Route::post('/admin/profile', 'App\Http\Controllers\Admin\AuthController@profile_update');
            Route::get('/logout', 'App\Http\Controllers\AuthController@logout');
            Route::get('/admin/profile/check', 'App\Http\Controllers\Admin\AuthController@usercheck'); 
            Route::get('/admin/dashboard','App\Http\Controllers\Admin\DashboardController@index');


                                                        /// Dashboard \\\


            Route::group(['prefix' => '/admin/dashboard'], function() {
                Route::controller(App\Http\Controllers\Admin\DashboardController::class)->group(function () {
                    Route::get('show','index');
                });
            });



                                            /// Category \\\

            Route::group(['prefix' => '/admin/category/'], function() {
                Route::controller(App\Http\Controllers\Admin\CategoryController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });


                                                    /// Brand \\\

            Route::group(['prefix' => '/admin/brand/'], function() {
                Route::controller(App\Http\Controllers\Admin\BrandController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });


                                                        /// Model \\\

                    Route::group(['prefix' => '/admin/model/'], function() {
                    Route::controller(App\Http\Controllers\Admin\ModelController::class)->group(function () {
                        Route::get('show','index');
                        Route::post('create','create');
                        Route::post('update','update');
                        Route::get('delete/{id}','delete');
                        Route::post('multi_delete','multi_delete');
                    });
                });


                                                        /// Deal \\\

                Route::group(['prefix' => '/admin/deal/'], function() {
                Route::controller(App\Http\Controllers\Admin\DealController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });



                                                    /// Banner \\\

            Route::group(['prefix' => '/admin/banner/'], function() {
                Route::controller(App\Http\Controllers\Admin\BannerController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });

            
                                                    ///Home Banner \\\

            Route::group(['prefix' => '/admin/allbanners/'], function() {
                Route::controller(App\Http\Controllers\Admin\AllBannerController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                });
            });


                                                        /// Bolg Category \\\

            Route::group(['prefix' => '/admin/blog_category/'], function() {
                Route::controller(App\Http\Controllers\Admin\BlogCategoryController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });

                                                            /// Bolg  \\\

            Route::group(['prefix' => '/admin/blog/'], function() {
                Route::controller(App\Http\Controllers\Admin\BlogController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });





                                            /// Product \\\

            Route::group(['prefix' => '/admin/product/'], function() {
                Route::controller(App\Http\Controllers\Admin\ProductController::class)->group(function () {
                    Route::get('show','index');
                    Route::get('admin_products','admin_products');
                    Route::get('seller_products','seller_products');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::get('is_approved/{id}','is_approved');
                    Route::get('is_featured/{id}','is_featured');
                    Route::get('is_published/{id}','is_published');
                    Route::post('multi_delete','multi_delete');
                    Route::post('multi_publish','is_multiple_published');
                });
            });

                                                            /// Varient \\\

            Route::group(['prefix' => '/admin/varient/'], function() {
                Route::controller(App\Http\Controllers\Admin\VarientController::class)->group(function () {
                    Route::post('delete','delete');
                });
            });

                                                    ///Wholesale Product \\\

            Route::group(['prefix' => '/admin/wholesale_product/'], function() {
                Route::controller(App\Http\Controllers\Admin\WholeSaleProductController::class)->group(function () {
                    Route::get('show','index');
                    Route::get('admin_products','admin_products');
                    Route::get('seller_products','seller_products');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::get('is_approved/{id}','is_approved');
                    Route::get('is_featured/{id}','is_featured');
                    Route::get('is_published/{id}','is_published');
                });
            });

                                            /// Order \\\

            Route::group(['prefix' => '/admin/order/'], function() {
                Route::controller(App\Http\Controllers\Admin\OrderController::class)->group(function () {
                    Route::get('show','index');
                    Route::get('admin_orders/{id}','admin_orders');
                    Route::get('seller_orders/{id}','seller_orders');
                    Route::post('delivery_status','delivery_status');
                    Route::post('payment_status','payment_status');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });

                                                /// Coupon \\\

                Route::group(['prefix' => '/admin/coupon/'], function() {
                Route::controller(App\Http\Controllers\Admin\CouponController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                });
            });

                                                        /// States \\\

            Route::group(['prefix' => '/admin/state/'], function() {
                Route::controller(App\Http\Controllers\Admin\StateController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });

                                                    /// Refund \\\

                Route::group(['prefix' => '/admin/refund/'], function() {
                Route::controller(App\Http\Controllers\Admin\RefundController::class)->group(function () {
                    Route::get('show','index');
                    Route::get('approved','approved_refunds');
                    Route::get('rejected','rejected_refunds');
                    Route::post('stripe_refund','stripe_refund');
                    Route::post('paypal_refund','paypal_refund');

                });
            });

            Route::group(['prefix' => '/admin/refund/'], function() {
                Route::controller(App\Http\Controllers\Admin\PaypalController::class)->group(function () {
                    Route::post('paypal_refund','processRefund');
                });
            });

            
                                                    /// Payout \\\

                    Route::group(['prefix' => '/admin/payout/'], function() {
                        Route::controller(App\Http\Controllers\Admin\PayoutController::class)->group(function () {
                            Route::get('show','index');
                            Route::get('status/{payout_id}','status');
                            Route::get('delete/{id}','delete'); 
                        });
                    });

                                                        /// Refund Time \\\

                Route::group(['prefix' => '/admin/refund_time/'], function() {
                Route::controller(App\Http\Controllers\Admin\RefundTimeController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('update','createOrupdate');
                });
            });


                                            /// Package \\\

            Route::group(['prefix' => '/admin/package/'], function() {
                Route::controller(App\Http\Controllers\Admin\PackageController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
            });


                                                    /// Customer \\\

                Route::group(['prefix' => '/admin/customer/'], function() {
                    Route::controller(App\Http\Controllers\Admin\CustomerController::class)->group(function () {
                        Route::get('show','index');
                        Route::get('is_active/{id}','is_active');
                        Route::get('delete/{id}','delete');
                        Route::post('multi_delete','multi_delete');
                    });
                });


                                                    /// Seller \\\

                Route::group(['prefix' => '/admin/seller/'], function() {
                    Route::controller(App\Http\Controllers\Admin\SellerController::class)->group(function () {
                        Route::get('show','index');
                        Route::get('is_active/{id}','is_active');
                        Route::get('is_verify/{id}','is_verify');
                        Route::get('delete/{id}','delete');
                        Route::post('multi_delete','multi_delete');
                    });
                });


                                            /// Seller \\\

                Route::group(['prefix' => '/admin/stores/'], function() {
                    Route::controller(App\Http\Controllers\Admin\ShopController::class)->group(function () {
                        Route::get('show','index');
                    });
                });


                                                    /// Report \\\

            Route::group(['prefix' => '/admin/report/'], function() {
                Route::controller(App\Http\Controllers\Admin\ReportController::class)->group(function () {
                    Route::post('admin_product_sale','admin_product_sale');
                    Route::post('saller_product_sale','saller_product_sale');
                    Route::post('product_stock','product_stock');
                    Route::post('product_wishlist','product_wishlist');
                });
            });



                                                    /// Subscriber \\\

        Route::group(['prefix' => '/admin/subscribe/'], function() {
            Route::controller(App\Http\Controllers\Admin\SubscriberController::class)->group(function () {
                Route::get('show','index');    
                Route::get('delete/{id}','delete'); 
                Route::post('multi_delete','multi_delete'); 
            });
        });



                                                    /// TermCondition \\\

            Route::group(['prefix' => '/admin/term_condition/'], function() {
                Route::controller(App\Http\Controllers\Admin\TermConditionController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('createOrUpdate','createOrUpdate');
                });
            });  
    
    
                                                        /// PrivacyPolicy \\\
    
            Route::group(['prefix' => '/admin/privacy_policy/'], function() {
                Route::controller(App\Http\Controllers\Admin\PrivacyPolicyController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('createOrUpdate','createOrUpdate');
                });
            }); 


                                                                    /// ReturnPolicy \\\

            Route::group(['prefix' => '/admin/return_policy/'], function() {
                Route::controller(App\Http\Controllers\Admin\ReturnPolicyController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('createOrUpdate','createOrUpdate');
                });
            }); 
            
                                                                /// Disclaimer \\\
    
            Route::group(['prefix' => '/admin/disclaimer/'], function() {
                Route::controller(App\Http\Controllers\Admin\DisclaimerController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('createOrUpdate','createOrUpdate');
                });
            });  


                                                /// Contact us \\\

        Route::group(['prefix' => '/admin/contactus'], function() {
            Route::controller(App\Http\Controllers\Admin\ContactUsController::class)->group(function () {   
                Route::post('show','index');
            });
        });


                                                    /// F&Qs \\\

        Route::group(['prefix' => '/admin/f&qs/'], function() {
            Route::controller(App\Http\Controllers\Admin\FandQController::class)->group(function () {
                Route::get('show','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
                Route::post('multi_delete','multi_delete');
            });
        });




                                /// Commission \\\

        Route::group(['prefix' => '/admin/commission/'], function() {
            Route::controller(App\Http\Controllers\Admin\CommissionController::class)->group(function () {
                Route::get('show','index');
                Route::post('createOrUpdate','createOrUpdate');
            });
        });




                                /// Seller F&Qs \\\

        Route::group(['prefix' => '/admin/seller_f&qs/'], function() {
            Route::controller(App\Http\Controllers\Admin\SellerFandQController::class)->group(function () {
                Route::get('show','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
                Route::post('multi_delete','multi_delete');
            });
        });




                            /// Seller Guide Videos \\\

        Route::group(['prefix' => '/admin/seller_guide_video/'], function() {
            Route::controller(App\Http\Controllers\Admin\SellerGuideVideoController::class)->group(function () {
                Route::get('show','index');
                Route::post('create','create');
                Route::post('update','update');
                Route::get('delete/{id}','delete');
                Route::post('multi_delete','multi_delete');
            });
        });



                                /// Website FeedBack \\\

        Route::group(['prefix' => 'admin/website_feedback'], function() {
            Route::controller(App\Http\Controllers\Admin\WebsiteFeedBackController::class)->group(function () {
                Route::get('show','index');
                Route::post('multi_delete','multi_delete');
            });
        }); 






        });

        Route::middleware(['seller'])->group(function () {



            /////////////////////////////////// Seller Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

            Route::get('/seller/profile/view/{id}', 'App\Http\Controllers\Seller\AuthController@profile_view');
            Route::post('/seller/profile/update', 'App\Http\Controllers\Seller\AuthController@profile_update');
            Route::get('/logout', 'App\Http\Controllers\AuthController@logout');
            Route::get('/seller/delete_selling/{id}', 'App\Http\Controllers\Seller\AuthController@delete_selling_platforms');
            Route::get('/seller/delete_social/{id}', 'App\Http\Controllers\Seller\AuthController@delete_social_platforms');
            Route::get('/seller/profile/check', 'App\Http\Controllers\Seller\AuthController@usercheck'); 
            Route::get('/seller/dashboard','App\Http\Controllers\Seller\DashboardController@index');

                                            /// Dashboard \\\


            Route::group(['prefix' => '/seller/dashboard'], function() {
                Route::controller(App\Http\Controllers\Seller\DashboardController::class)->group(function () {
                    Route::get('show/{id}','index');
                    Route::get('shop/{shop_id}','searchByshop');

                });
            });



                                            /// Product \\\

            Route::group(['prefix' => '/seller/product/'], function() {
                Route::controller(App\Http\Controllers\Seller\ProductController::class)->group(function () {
                    Route::get('show/{id}','index');
                    Route::post('create','create');
                    // Route::get('sell_similar/{id}','sell_similar');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                    Route::get('is_published/{id}','is_published');
                    Route::post('is_featured','is_featured');
                    Route::post('multi_delete','multi_delete');
                    Route::post('multi_publish','is_multiple_published');
                    Route::post('multi_draft','is_multiple_draft');
                    Route::post('multi_featured','is_multiple_featured');
                    Route::post('multi_unfeatured','is_multiple_unfeatured');
                    Route::get('gallery_delete/{id}','gallery_delete');
                });
            });


                                                         /// Varient \\\

            Route::group(['prefix' => '/seller/varient/'], function() {
                Route::controller(App\Http\Controllers\Seller\VarientController::class)->group(function () {
                    Route::post('delete','delete');
                });
            });

                                                /// Shop \\\

                Route::group(['prefix' => '/seller/shop/'], function() {
                Route::controller(App\Http\Controllers\Seller\ShopController::class)->group(function () {
                    Route::get('show/{seller_id}','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('delete/{id}','delete');
                });
            });


                                                        ///Wholesale Product \\\

                Route::group(['prefix' => '/admin/wholesale_product/'], function() {
                    Route::controller(App\Http\Controllers\Admin\WholeSaleProductController::class)->group(function () {
                        Route::get('show/{id}','index');
                        Route::post('create','create');
                        Route::post('update','update');
                        Route::get('delete/{id}','delete');
                        // Route::get('is_approved/{id}','is_approved');
                        // Route::get('is_featured/{id}','is_featured');
                        Route::get('is_published/{id}','is_published');
                    });
                });


                                                    /// Order \\\

                Route::group(['prefix' => '/seller/order/'], function() {
                Route::controller(App\Http\Controllers\Seller\OrderController::class)->group(function () {
                    Route::get('show/{id}','index');
                    Route::post('delivery_status','delivery_status');
                    Route::post('payment_status','payment_status');
                    Route::post('tags','update_tags');
                    Route::get('delete/{id}','delete');
                    Route::post('multi_delete','multi_delete');
                });
             });



                                                             /// Coupon \\\

                Route::group(['prefix' => '/seller/coupon/'], function() {
                Route::controller(App\Http\Controllers\Seller\CouponController::class)->group(function () {
                    Route::get('show/{seller_id}','index');
                    Route::post('create','create');
                    Route::post('update','update');
                    Route::get('status/{id}','status');
                    Route::get('delete/{id}','delete');
                });
            });



                                            /// Package \\\

            Route::group(['prefix' => '/seller/package/'], function() {
                Route::controller(App\Http\Controllers\Seller\PackageController::class)->group(function () {
                    Route::get('show','index');
                    Route::post('subscribe','subscribe');
                    Route::get('subscribe_user/{id}','subscribeUser');

                });
            });


                                                    /// Payout \\\

                Route::group(['prefix' => '/seller/payout/'], function() {
                    Route::controller(App\Http\Controllers\Seller\PayoutController::class)->group(function () {
                        Route::get('show/{id}','index');
                        Route::post('create','create');
                        Route::get('delete/{id}','delete');
            
                    });
                });


                                        // Refund
                
                    Route::group(['prefix' => '/seller/refund/'], function() {
                        Route::controller(App\Http\Controllers\Seller\RefundController::class)->group(function () {
                            Route::get('show/{seller_id}','index');
                            Route::get('is_approved/{refund_id}','is_approved');
                        });
                    });

                                                            // MyCustomers
                
                    Route::group(['prefix' => '/seller/customers/'], function() {
                        Route::controller(App\Http\Controllers\Seller\MyCustomerController::class)->group(function () {
                            Route::get('show/{seller_id}','index');
        
                        });
                    });


                                                                                // Billings
                
                    Route::group(['prefix' => '/seller/billing/'], function() {
                        Route::controller(App\Http\Controllers\Seller\BillingController::class)->group(function () {
                            Route::get('show/{seller_id}','index');
        
                        });
                    });


                                                // Customer Query
                                    
                    Route::group(['prefix' => '/seller/customers_query/'], function() {
                        Route::controller(App\Http\Controllers\Seller\CustomerQueryController::class)->group(function () {
                            Route::get('show/{seller_id}','index');
                            Route::post('reply','reply');
                            Route::post('multi_delete','multi_delete');
                            Route::post('multi_read','multi_read');
                            Route::post('multi_unread','multi_unread');
                        });
                    });

 


        });


}); 

        /////////////////////////////////// Customer Routes \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

        Route::get('/customer/profile/view/{id}', 'App\Http\Controllers\Customer\AuthController@profile_view');
        Route::post('/customer/profile/update', 'App\Http\Controllers\Customer\AuthController@profile_update');
        Route::get('/logout', 'App\Http\Controllers\AuthController@logout');
        Route::get('/customer/profile/check', 'App\Http\Controllers\Customer\AuthController@usercheck'); 
        Route::get('/customer/dashboard','App\Http\Controllers\Customer\DashboardController@index');


        Route::post('login/social', 'App\Http\Controllers\Customer\AuthController@social_login');


        // Social Login Routes
            Route::get('login/{provider}', 'App\Http\Controllers\Customer\SocialLoginController@redirectToProvider')->middleware('web');;
            Route::get('login/{provider}/callback', 'App\Http\Controllers\Customer\SocialLoginController@handleProviderCallback')->middleware('web');


                                    /// Home \\\

        Route::group(['prefix' => '/home'], function() {
            Route::controller(App\Http\Controllers\Customer\HomeController::class)->group(function () {
                Route::get('','index');
                Route::get('load_more/{length}','load_more');
                Route::get('load_more_top_selling/{length}','load_more_top_selling');
                Route::get('load_more_trending/{length}','load_more_trending');
                Route::get('load_more_featured/{length}','load_more_featured');
            });
        });


                                            /// CategoryProducts \\\

        Route::group(['prefix' => '/category_products'], function() {
            Route::controller(App\Http\Controllers\Customer\CategoryProductController::class)->group(function () {
                Route::get('show/{category_id}','index');
                Route::get('load_more/{category_id}/{length}','load_more');
            });
        });

                                            /// BrandProducts \\\

        Route::group(['prefix' => '/brand_products'], function() {
            Route::controller(App\Http\Controllers\Customer\BrandProductController::class)->group(function () {
                Route::get('show/{brand_id}','index');
                Route::get('load_more/{brand_id}/{length}','load_more');
            });
        });


                                                    /// ModelProducts \\\

        Route::group(['prefix' => '/model_products'], function() {
            Route::controller(App\Http\Controllers\Customer\ModelProductController::class)->group(function () {
                Route::get('show/{model_id}','index');
                Route::get('load_more/{model_id}/{length}','load_more');
            });
        });


                                                            /// ShopProducts \\\

        Route::group(['prefix' => '/shop_products'], function() {
            Route::controller(App\Http\Controllers\Customer\ShopProductController::class)->group(function () {
                Route::get('show/{shop_id}','index');
                Route::get('load_more/{shop_id}/{length}','load_more');
            });
        });
                                                            
        


                                               /// Product \\\

      Route::group(['prefix' => '/product'], function() {
        Route::controller(App\Http\Controllers\Customer\ProductController::class)->group(function () {
            Route::get('show','index');
            Route::post('comment','comment');
            Route::post('rating','rating');
            Route::get('detail/{id}','detail');
        });
    });

                                                   /// Address \\\

        Route::group(['prefix' => '/address'], function() {
        Route::controller(App\Http\Controllers\Customer\AddressController::class)->group(function () {
            Route::get('show/{id}','index');
            Route::post('create','create');
            Route::post('update','update');
            Route::get('delete/{id}','delete');
        });
    });


                                                       /// Cart \\\

        Route::group(['prefix' => '/cart'], function() {
        Route::controller(App\Http\Controllers\Customer\CartController::class)->group(function () {
            Route::get('show/{customer_id}','index');
            Route::post('create','create');
            Route::post('update','update');
            Route::get('delete/{cart_id}','delete');
            Route::get('clear/{customer_id}','clear');
        });
    });

                                                   /// Product Review \\\

        Route::group(['prefix' => '/review'], function() {
        Route::controller(App\Http\Controllers\Customer\ReviewController::class)->group(function () {   
            Route::post('create','create');
        });
    });

                                                       /// Contact us \\\

        Route::group(['prefix' => '/contactus'], function() {
        Route::controller(App\Http\Controllers\Customer\ContactUsController::class)->group(function () {   
            Route::post('create','create');
        });
    });


                                          /// Wishlist \\\

        Route::group(['prefix' => 'wishlist/'], function() {
        Route::controller(App\Http\Controllers\Customer\WhishlistController::class)->group(function () {
            Route::get('show/{id}','index');
            Route::post('create','create');
            Route::get('delete/{id}','delete');
        });
    });


                                        /// Subscribe \\\

        Route::group(['prefix' => 'subscribe/'], function() {
        Route::controller(App\Http\Controllers\Customer\SubscriberController::class)->group(function () {
            Route::post('create','create');     
        });
    });


                                        /// Order \\\

        Route::group(['prefix' => 'order/'], function() {
        Route::controller(App\Http\Controllers\Customer\OrderController::class)->group(function () {
            Route::get('show/{id}','index');
            Route::post('create','create');
        });
    });


                                            /// Refund \\\

    Route::group(['prefix' => 'refund/'], function() {
        Route::controller(App\Http\Controllers\Customer\RefundController::class)->group(function () {
            Route::get('show/{customer_id}','index');
            Route::post('create','create');
            Route::get('delete/{id}','delete');
        });
    });

                                                    /// Coupon \\\

    Route::group(['prefix' => 'coupon/'], function() {
        Route::controller(App\Http\Controllers\Customer\CouponController::class)->group(function () {
            Route::post('apply','apply');
        });
    });


                                                /// Filter \\\

    Route::group(['prefix' => 'filter/'], function() {
        Route::controller(App\Http\Controllers\Customer\FilterController::class)->group(function () {
            Route::post('product_search','search');  
            Route::post('target_search','target_search'); 
            Route::post('multi_search','multisearch');   
        });
    });

                                                    /// Payment \\\

    Route::group(['prefix' => 'payment/'], function() {
        Route::controller(App\Http\Controllers\Customer\PaymentController::class)->group(function () {
            Route::post('stripe','stripe_payment');    
            Route::post('paypal','paypal_payment');    
        });
    });

    Route::get('/paypal/success', 'App\Http\Controllers\Customer\PaymentController@paypalSuccess')->name('paypal.success');



                                                /// TermCondition \\\

        Route::group(['prefix' => 'term_condition/'], function() {
            Route::controller(App\Http\Controllers\Customer\TermConditionController::class)->group(function () {
                Route::get('web/show','web_index');
                Route::get('app/show','app_index');
            });
        });  


                                                    /// PrivacyPolicy \\\

        Route::group(['prefix' => 'privacy_policy/'], function() {
            Route::controller(App\Http\Controllers\Customer\PrivacyPolicyController::class)->group(function () {
                Route::get('web/show','web_index');
                Route::get('app/show','app_index');
            });
        }); 
        
                                                            /// Disclaimer \\\

        Route::group(['prefix' => 'disclaimer/'], function() {
            Route::controller(App\Http\Controllers\Customer\DisclaimerController::class)->group(function () {
                Route::get('web/show','web_index');
                Route::get('app/show','app_index');
            });
        });  


                                                        /// F&Qs \\\

            Route::group(['prefix' => 'f&qs/'], function() {
                Route::controller(App\Http\Controllers\Customer\FandQController::class)->group(function () {
                    Route::get('show','index');
                    Route::get('seller','index2');
                });
            });  


                                                                    /// Return Policy \\\

            Route::group(['prefix' => 'return_policy/'], function() {
                Route::controller(App\Http\Controllers\Customer\ReturnPolicyController::class)->group(function () {
                    Route::get('web/show','web_index');
                    Route::get('app/show','app_index');
                });
            });  

                                            /// Notification \\\

            Route::group(['prefix' => 'notifications/'], function() {
                Route::controller(App\Http\Controllers\Customer\NotificationController::class)->group(function () {
                    Route::get('show/{customer_id}','index');
                    Route::get('delete/{notification_id}','delete');
                });
            });  



                                                        /// SellerContact \\\

                Route::group(['prefix' => 'product/query/'], function() {
                    Route::controller(App\Http\Controllers\Customer\SellerContactController::class)->group(function () {
                        Route::post('send','send');
                    });
                });
                
                                                        /// Website FeedBack \\\

                Route::group(['prefix' => 'website_feedback'], function() {
                    Route::controller(App\Http\Controllers\Customer\WebsiteFeedBackController::class)->group(function () {
                        Route::post('send','create');
                    });
                }); 
