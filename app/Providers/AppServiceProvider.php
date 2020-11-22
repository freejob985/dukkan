<?php

namespace App\Providers;

use App\AffiliateUser;
use App\Uploadpres;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

use  View;
class AppServiceProvider extends ServiceProvider
{

  public function boot()
  {
    Schema::defaultStringLength(191);

    View::share('prescription',  Uploadpres::where('status', '=', '0')->count());

    View::share('affilatecount',  AffiliateUser::where('status', '=', '0')->count());
  }


  public function register()
  {
    //
  }
}
