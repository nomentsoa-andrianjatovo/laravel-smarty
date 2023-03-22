<?php

namespace Noum\Smarty;

use Illuminate\Support\ServiceProvider;
use Smarty;

class SmartyServiceProvider extends ServiceProvider
{
    public function register()
    {
        // register a singleton instance of the Smarty class enabling the use of something like Smarty::display()...
        $this->app->singleton('smarty', function () {
            $smarty = new Smarty();
            // get the relative path to the current tenant's website folder
            $currentTenant = base_path(tenantPath());
            // check if the site is in production mode
            $isProd = isProd();
            // if in production mode, use the "prod" subfolder, otherwise use the "dev" subfolder
            $templatesDir = $isProd ? config('floowedit.folder.prod') : config('floowedit.folder.editing');
            // set the template directory to the appropriate subfolder of the current tenant
            $smarty->setTemplateDir($currentTenant . '/' . $templatesDir);
            // add a second template directory for the "shared" subfolder of the current tenant
            $smarty->addTemplateDir($currentTenant . '/'.config('floowedit.folder.shared'));
            // add a third template directory for the typical Laravel views folder
            $smarty->addTemplateDir(resource_path('views'));
            // set the compile and cache directories to the "templates_c" subfolder of the current tenant
            $smarty->setCompileDir($currentTenant . '/templates_c');
            $smarty->setCacheDir($currentTenant . '/templates_c');
            // set the config directory to the "upload/files" directory inside the current tenant folder
            $config_dir = $currentTenant . '/'.config('floowedit.folder.upload').'/files/';
            $smarty->setConfigDir($config_dir);
            // load the all.conf file if it exists
            if (file_exists($config_dir . 'all.conf')) {
                $smarty->configLoad('all.conf');
            }
            // if not in production mode, disable caching
            if (!$isProd) {
                $smarty->caching = Smarty::CACHING_OFF;
            }
            // Load custom smarty plugins
            $smarty->addPluginsDir(resource_path('smarty/plugins'));
            return $smarty;
        });
    }
}
