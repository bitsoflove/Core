<?php namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Support\Facades\Config;
use Mcamara\LaravelLocalization\LaravelLocalization;

class LocalizationMiddleware implements Middleware {

    public function handle( $request, Closure $next )
    {
        //check if multi site tenancy is on, and if not, execute the original logic
        if (!is_module_enabled('Site')) {
            $original = \App::make('Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter');
            return $original->handle($request, $next);
        }

        $this->setAdminOrSiteLocale();

        return $next($request);
    }

    private function setAdminOrSiteLocale() {
        //Set Locale variable, unless we're currently in the admin context
        //then we set the admin locale

        $inAdminContext = $this->inAdminContext();

        if($inAdminContext) {
            $this->setAdminLocale();
        }

        return \Site::setLocale();
    }


    private function inAdminContext() {
        $adminPrefix = Config::get('asgard.core.core.admin-prefix');
        $firstSegment = \Illuminate\Support\Facades\Request::segment(1);
        return ($firstSegment === $adminPrefix);
    }

    private function setAdminLocale() {
        $defaultLocale = Config::get('app.locale');
        \App('laravellocalization')->setLocale($defaultLocale);
        \App::setLocale($defaultLocale);
    }
}
