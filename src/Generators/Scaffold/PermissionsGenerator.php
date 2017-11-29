<?php

namespace InfyOm\Generator\Generators\Scaffold;

use InfyOm\Generator\Common\CommandData;
use InfyOm\Generator\Generators\BaseGenerator;
use InfyOm\Generator\Utils\FileUtil;

class PermissionsGenerator extends BaseGenerator
{
    /** @var CommandData */
    private $commandData;

    /** @var string */
    private $path;

    /** @var string */
    private $permissionsSeedFileName;

    public function __construct(CommandData $commandData)
    {
        $this->commandData = $commandData;
        $this->path = $commandData->config->pathSeed;
        $this->permissionsSeedFileName = $this->commandData->dynamicVars['$MODEL_NAME$'].'PermissionsTableSeeder.php';
    }

    public function generate()
    {
        $templateData = get_template('permissions.seed', 'laravel-generator');

        $templateData = fill_template($this->commandData->dynamicVars, $templateData);

        FileUtil::createFile($this->path, $this->permissionsSeedFileName, $templateData);

        $this->commandData->commandComment("\nPermissions Seeder created: ");
        $this->commandData->commandInfo($this->permissionsSeedFileName);
    }

    public function rollback()
    {
        if ($this->rollbackFile($this->path, $this->createFileName)) {
            $this->commandData->commandComment('Create permissions seed files deleted: '.$this->permissionsSeedFileName);
        }
    }
}
