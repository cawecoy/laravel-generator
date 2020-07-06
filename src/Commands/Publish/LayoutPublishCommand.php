<?php

namespace InfyOm\Generator\Commands\Publish;

use InfyOm\Generator\Utils\FileUtil;
use InfyOm\Generator\Common\CommandData;

class LayoutPublishCommand extends PublishBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'infyom.publish:layout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes auth files';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->copyFiles();
        $this->updateRoutes();
        $this->publishHomeController();
    }

    private function copyFiles()
    {
        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);

        $viewsPath = config('infyom.laravel_generator.path.views', base_path('resources/views/'));
        $publicPath = config('infyom.laravel_generator.path.public', base_path('public/'));
        $langPath = config('infyom.laravel_generator.path.language', base_path('resources/lang/'));
        $locale = config('infyom.laravel_generator.locale', 'en');
        $templateType = config('infyom.laravel_generator.templates', 'core-templates');

        $this->createDirectories($viewsPath, $publicPath, $langPath, $locale);

        $files = $this->getFiles($viewsPath, $publicPath, $langPath, $locale);

        foreach ($files as $stub => $destinationFile) {
            $sourceFile = get_template_file_path('scaffold/'.$stub, $templateType);
            $this->publishFile($sourceFile, $destinationFile, $destinationFile);
        }
    }

    private function createDirectories($viewsPath, $publicPath, $langPath, $locale)
    {
        FileUtil::createDirectoryIfNotExist($viewsPath.'layouts');
        FileUtil::createDirectoryIfNotExist($viewsPath.'auth');

        FileUtil::createDirectoryIfNotExist($viewsPath.'auth/passwords');
        FileUtil::createDirectoryIfNotExist($viewsPath.'auth/emails');

        FileUtil::createDirectoryIfNotExist($publicPath.'vendor/laravel-generator');

        FileUtil::createDirectoryIfNotExist($langPath.$locale.'/admin');
    }

    private function getFiles($viewsPath, $publicPath, $langPath, $locale)
    {
        return [
            'layouts/datepicker'            => $langPath.$locale.'/admin/datepicker.php',
            'layouts/form-submission'       => $publicPath.'vendor/laravel-generator/form-submission.js',
            'layouts/app'                   => $viewsPath.'layouts/app.blade.php',
            'layouts/sidebar'               => $viewsPath.'layouts/sidebar.blade.php',
            'layouts/datatables_css'        => $viewsPath.'layouts/datatables_css.blade.php',
            'layouts/datatables_js'         => $viewsPath.'layouts/datatables_js.blade.php',
            'layouts/menu'                  => $viewsPath.'layouts/menu.blade.php',
            'layouts/home'                  => $viewsPath.'home.blade.php',
            'partials/breadcrumbs_no_html'  => $viewsPath.'partials/breadcrumbs_no_html.blade.php',
            'auth/login'                    => $viewsPath.'auth/login.blade.php',
            'auth/register'                 => $viewsPath.'auth/register.blade.php',
            'auth/email'                    => $viewsPath.'auth/passwords/email.blade.php',
            'auth/reset'                    => $viewsPath.'auth/passwords/reset.blade.php',
            'emails/password'               => $viewsPath.'auth/emails/password.blade.php',
        ];
    }

    private function getScripts()
    {
        return [
        ];
    }

    private function updateRoutes()
    {
        $path = config('infyom.laravel_generator.path.routes', app_path('routes/web.php'));

        $prompt = 'Existing routes web.php file detected. Should we add standard auth routes? (y|N) :';
        if (file_exists($path) && !$this->confirmOverwrite($path, $prompt)) {
            return;
        }

        $routeContents = file_get_contents($path);

        $routesTemplate = get_template('routes.auth', 'laravel-generator');

        $routeContents .= "\n\n".$routesTemplate;

        file_put_contents($path, $routeContents);
        $this->comment("\nRoutes added");
    }

    private function publishHomeController()
    {
        $templateData = get_template('home_controller', 'laravel-generator');

        $templateData = $this->fillTemplate($templateData);

        $controllerPath = config('infyom.laravel_generator.path.controller', app_path('Http/Controllers/'));

        $fileName = 'HomeController.php';

        if (file_exists($controllerPath.$fileName) && !$this->confirmOverwrite($fileName)) {
            return;
        }

        FileUtil::createFile($controllerPath, $fileName, $templateData);

        $this->info('HomeController created');
    }

    /**
     * Replaces dynamic variables of template.
     *
     * @param string $templateData
     *
     * @return string
     */
    private function fillTemplate($templateData)
    {
        $templateData = str_replace(
            '$NAMESPACE_CONTROLLER$',
            config('infyom.laravel_generator.namespace.controller'), $templateData
        );

        $templateData = str_replace(
            '$NAMESPACE_REQUEST$',
            config('infyom.laravel_generator.namespace.request'), $templateData
        );

        return $templateData;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }
}
