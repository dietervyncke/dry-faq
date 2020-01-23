<?php

namespace Tnt\Faq;

use Oak\Contracts\Config\RepositoryInterface;
use Oak\Contracts\Container\ContainerInterface;
use Oak\Migration\MigrationManager;
use Oak\Migration\Migrator;
use Oak\ServiceProvider;
use Tnt\Faq\Admin\FaqCategoryManager;
use Tnt\Faq\Contracts\FaqCategoryRepositoryInterface;
use Tnt\Faq\Repository\FaqCategoryRepository;
use Tnt\Faq\Revisions\CreateFaqCategoryTable;
use Tnt\Faq\Revisions\CreateFaqItemTable;

class FaqServiceProvider extends ServiceProvider
{
    public function register(ContainerInterface $app)
    {
        $app->set(FaqCategoryRepositoryInterface::class, FaqCategoryRepository::class);
    }

    public function boot(ContainerInterface $app)
    {
        if ($app->isRunningInConsole()) {

            $migrator = $app->getWith(Migrator::class, [
                'name' => 'faq'
            ]);

            $migrator->setRevisions([
                CreateFaqCategoryTable::class,
                CreateFaqItemTable::class,
            ]);

            $app->get(MigrationManager::class)
                ->addMigrator($migrator)
            ;
        }

        $this->registerAdminModules($app);
    }

    /**
     * @param ContainerInterface $app
     */
    private function registerAdminModules(ContainerInterface $app)
    {
        $languages = $app->get(RepositoryInterface::class)->get('faq.languages', [
            'nl',
            'fr',
            'en'
        ]);

        array_unshift(\dry\admin\Router::$modules, new FaqCategoryManager($languages));
    }
}