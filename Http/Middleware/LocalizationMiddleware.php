<?php namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Middleware;
use Mcamara\LaravelLocalization\LaravelLocalization;

class LocalizationMiddleware implements Middleware {

  public function handle( $request, Closure $next )
  {
      //check if multi site tenancy is on, and if not, execute the original logic
      if (!is_module_enabled('Site')) {
          $original = \App::make('Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter');
          return $original->handle($request, $next);
      }

      //Set Locale variable
      \Site::setLocale();
      return $next($request);
  }
}
