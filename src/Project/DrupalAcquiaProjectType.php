<?php

namespace Droath\ProjectX\Acquia\Project;

use Droath\ConsoleForm\Field\TextField;
use Droath\ProjectX\Project\DrupalProjectType;
use Droath\ProjectX\Utility;

/**
 * Define Drupal acquia project type.
 */
class DrupalAcquiaProjectType extends DrupalProjectType
{
    /**
     * {@inheritdoc}
     */
    public function optionForm()
    {
        return parent::optionForm()
            ->addFields([
                (new TextField('application_name', 'Input the acquia application name'))
            ]);
    }

    /**
     * Setup Acquia pipeline.
     *
     * The setup process consist of the following:
     *   Copy acquia-pipeline.yml template to project root.
     */
    public function setupPipeline()
    {
        $this->copyTemplateFileToProject('acquia-pipelines.yml');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTypeId()
    {
        return 'drupal:acquia';
    }

    /**
     * {@inheritdoc}
     */
    public static function getLabel()
    {
        return 'Drupal Acquia';
    }

    /**
     * {@inheritdoc}
     */
    public function templateDirectories()
    {
        return array_merge([
            $this->getBasePath() . '/templates/acquia'
        ], parent::templateDirectories());
    }

    /**
     * Override \Droath\ProjectX\Project\buildSteps.
     */
    protected function buildSteps()
    {
        parent::buildSteps();

        $this->setupPipeline();

        return $this;
    }

    /**
     * Override \Droath\ProjectX\Project\uncommentedIfSettingsLocal.
     */
    protected function uncommentedIfSettingsLocal()
    {
        $application_name = $this->getApplicationName();

        $string = "if (file_exists('/var/www/site-php')) {\n  ";
        $string .= "include '/var/www/site-php/{$application_name}/{$application_name}-settings.inc';\n}\n";
        $string .= 'elseif (file_exists("{$app_root}/{$site_path}/settings.local.php"))' . " {\n  ";
        $string .= 'include "{$app_root}/{$site_path}/settings.local.php";' . "\n}";

        return $string;
    }

    /**
     * Get Acquia application name.
     */
    protected function getApplicationName()
    {
        $options = $this->getOptions();

        return strtolower(Utility::cleanString(
            $options['application_name'],
            '/[^a-zA-Z0-9]/'
        ));
    }

    /**
     * Get package base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        return dirname(dirname(__DIR__));
    }
}
