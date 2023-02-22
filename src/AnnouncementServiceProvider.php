<?php

namespace Corals\Modules\Announcement;

use Corals\Foundation\Providers\BasePackageServiceProvider;
use Corals\Modules\Announcement\Facades\Announcement;
use Corals\Modules\Announcement\Models\Announcement as AnnouncementModel;
use Corals\Modules\Announcement\Providers\AnnouncementAuthServiceProvider;
use Corals\Modules\Announcement\Providers\AnnouncementObserverServiceProvider;
use Corals\Modules\Announcement\Providers\AnnouncementRouteServiceProvider;
use Corals\Settings\Facades\Modules;
use Corals\Settings\Facades\Settings;
use Illuminate\Foundation\AliasLoader;

class AnnouncementServiceProvider extends BasePackageServiceProvider
{
    /**
     * @var
     */
    protected $defer = true;
    /**
     * @var
     */
    protected $packageCode = 'corals-announcement';

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function bootPackage()
    {
        // Load view
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Announcement');

        // Load translation
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'Announcement');

        // Load migrations
//        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->registerCustomFieldsModels();

        $this->app->booted(function () {
            // the assets packages is loaded after Announcement package
            // we wait after the application is booted then execute the related code
            \Assets::add(asset('assets/modules/announcement/plugins/magnific-popup/jquery.magnific-popup.min.js'));
            \Assets::add(asset('assets/modules/announcement/plugins/magnific-popup/magnific-popup.css'));
            \Assets::add(asset('assets/modules/announcement/js/ann-scripts.js'));
            \Assets::add(asset('assets/modules/announcement/css/ann-style.css'));
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerPackage()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/announcement.php', 'announcement');

        $this->app->register(AnnouncementRouteServiceProvider::class);
        $this->app->register(AnnouncementAuthServiceProvider::class);
        $this->app->register(AnnouncementObserverServiceProvider::class);
//        $this->app->register(AnnouncementEventServiceProvider::class);

        $this->app->booted(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Announcement', Announcement::class);
        });
    }

    protected function registerCustomFieldsModels()
    {
        Settings::addCustomFieldModel(AnnouncementModel::class);
    }

    public function registerModulesPackages()
    {
        Modules::addModulesPackages('corals/announcement');
    }
}
