<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Die Versions-Nummer im Klassenname sollte lt. Empfehlung die App-Version wiedergeben: 2.34.5 => 203405
 * s.a. https://docs.nextcloud.com/server/latest/developer_manual/basics/storage/migrations.html#console-commands
 *
 * Migration ausführen:
 * 1) sudo -u daemon php ./occ migrations:execute guitarsongbook 000001Date20230413123054
 * 2) evtl. notwendig: composer dump-autoload
 *
 * Die Migration wird beim Reload der App ausgeführt, wenn die App-Version erhöht wurde (s. ppinfo/info.xml).
 */

namespace OCA\GuitarSongbook\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000001Date20230413123054 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if ($schema->hasTable('guitarsongbook')) {
            $table = $schema->getTable('guitarsongbook');
        }
        else {
            $table = $schema->createTable('guitarsongbook');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->setPrimaryKey(['id']);
        }

        // Columns

        if (!$table->hasColumn('title')) {
            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
        }

        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 200,
            ]);
        }

        if (!$table->hasColumn('content')) {
            $table->addColumn('content', 'text', [
                'notnull' => true,
                'default' => ''
            ]);
        }

//        if (!$table->hasColumn('date_created')) {
//            $table->addColumn('date_created', 'datetime_immutable', [
//                'notnull' => false,
//            ]);
//        }
//
//        if (!$table->hasColumn('date_modified')) {
//            $table->addColumn('date_modified', 'datetime_immutable', [
//                'notnull' => false,
//            ]);
//        }

        // Index

        if (!$table->hasIndex('guitarsongbook_user_id_index')) {
            $table->addIndex(['user_id'], 'guitarsongbook_user_id_index');
        }

		return $schema;
	}
}
