<?php

namespace InfyOm\Generator\Generators\Scaffold;

use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Generators\BaseGenerator;
use InfyOm\Generator\Utils\FileUtil;

class BreadcrumbsGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $breadcrumbContents;

    /** @var string */
    private $breadcrumbsTemplate;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathBreadcrumbs;
        $this->breadcrumbContents = file_get_contents($this->path);
        $this->breadcrumbsTemplate = get_template('breadcrumbs.breadcrumbs', 'laravel-generator');
        $this->breadcrumbsTemplate = fill_template($this->commandData->dynamicVars, $this->breadcrumbsTemplate);
    }

    public function generate()
    {
        $this->breadcrumbContents .= "\n\n".$this->breadcrumbsTemplate;

        file_put_contents($this->path, $this->breadcrumbContents);
        $this->commandData->commandComment("\n".$this->commandData->config->mCamelPlural.' breadcrumbs added.');
    }

    public function rollback()
    {
        if (Str::contains($this->breadcrumbContents, $this->breadcrumbsTemplate)) {
            $this->breadcrumbContents = str_replace($this->breadcrumbsTemplate, '', $this->breadcrumbContents);
            file_put_contents($this->path, $this->breadcrumbContents);
            $this->commandData->commandComment('Breadcrumbs deleted');
        }
    }
}
