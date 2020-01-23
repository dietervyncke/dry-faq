<?php

namespace Tnt\Faq\Revisions;

use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\TableBuilder;

class CreateFaqCategoryTable extends DatabaseRevision implements RevisionInterface
{
    public function up()
    {
        $this->queryBuilder->table('faq_category')->create(function(TableBuilder $table) {

            $table->addColumn('id', 'int')->length(11)->primaryKey();
            $table->addColumn('created', 'int')->length(11);
            $table->addColumn('updated', 'int')->length(11);
            $table->addColumn('sort_string', 'varchar')->length(255);
            $table->addColumn('sort_index', 'int')->length(11);
            $table->addColumn('is_visible', 'tinyint')->length(1);
            $table->addColumn('title_nl', 'varchar')->length(255);
            $table->addColumn('title_fr', 'varchar')->length(255);
            $table->addColumn('title_en', 'varchar')->length(255);
            $table->addColumn('slug_nl', 'varchar')->length(255);
            $table->addColumn('slug_fr', 'varchar')->length(255);
            $table->addColumn('slug_en', 'varchar')->length(255);
            $table->addColumn('introduction_nl', 'varchar')->length(255);
            $table->addColumn('introduction_fr', 'varchar')->length(255);
            $table->addColumn('introduction_en', 'varchar')->length(255);
            $table->addColumn('body_nl', 'text');
            $table->addColumn('body_fr', 'text');
            $table->addColumn('body_en', 'text');
            $table->addColumn('parent', 'int')->length(11)->null();

            $table->addForeignKey('parent', 'faq_category');

        });

        $this->execute();
    }

    public function down()
    {
        $this->queryBuilder->table('faq_category')->drop();

        $this->execute();
    }

    public function describeUp(): string
    {
        return 'Table faq_category created';
    }

    public function describeDown(): string
    {
        return 'Table faq_category dropped';
    }
}