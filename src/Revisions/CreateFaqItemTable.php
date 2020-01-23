<?php

namespace Tnt\Faq\Revisions;

use Oak\Contracts\Migration\RevisionInterface;
use Tnt\Dbi\TableBuilder;

class CreateFaqItemTable extends DatabaseRevision implements RevisionInterface
{
    public function up()
    {
        $this->queryBuilder->table('faq_item')->create(function(TableBuilder $table) {

            $table->addColumn('id', 'int')->length(11)->primaryKey();
            $table->addColumn('created', 'int')->length(11);
            $table->addColumn('updated', 'int')->length(11);
            $table->addColumn('sort_index', 'int')->length(11);
            $table->addColumn('is_visible', 'tinyint')->length(1);
            $table->addColumn('question_nl', 'varchar')->length(255);
            $table->addColumn('question_fr', 'varchar')->length(255);
            $table->addColumn('question_en', 'varchar')->length(255);
            $table->addColumn('answer_nl', 'text');
            $table->addColumn('answer_fr', 'text');
            $table->addColumn('answer_en', 'text');
            $table->addColumn('faq_category', 'int')->length(11);

            $table->addForeignKey('faq_category', 'faq_category');

        });

        $this->execute();
    }

    public function down()
    {
        $this->queryBuilder->table('faq_item')->drop();

        $this->execute();
    }

    public function describeUp(): string
    {
        return 'Table faq_item created';
    }

    public function describeDown(): string
    {
        return 'Table faq_item dropped';
    }
}