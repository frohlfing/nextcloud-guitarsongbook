<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Die Versions-Nummer im Klassenname sollte lt. Empfehlung die App-Version wiedergeben: 2.34.5 => 203405
 * s.a. https://docs.nextcloud.com/server/latest/developer_manual/basics/storage/migrations.html#console-commands
 *
 * Migration ausführen:
 * 1) php ./occ migrations:execute guitarsongbook 000001Date20230413123054
 *    oder so als daemon:
 *      sudo -u daemon php ./occ migrations:execute guitarsongbook 000001Date20230413123054
 * 2) evtl. notwendig: composer dump-autoload
 *
 * Die Migration wird beim Reload der App ausgeführt, wenn die App-Version erhöht wurde (s. ppinfo/info.xml).
 *
 * Table management tips:
 * https://docs.nextcloud.com/server/latest/developer_manual/basics/storage/database.html#table-management-tips
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
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper
    {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

        // ---------------------------------------
        // guitarsongbook
        // ---------------------------------------

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

            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 128
            ]);
            $table->addIndex(['name'], 'guitarsongbook_name_index');

            $table->addColumn('user_id', 'string', [ // user id, e.g. "admin"
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addIndex(['user_id'], 'guitarsongbook_uid_index');
        }

        // Song Information (optional)
        foreach (['title', 'artist', 'subtitle', 'album', 'words', 'music', 'copyright', 'transcriber', 'notice', 'instructions'] as $column) {
            if (!$table->hasColumn($column)) {
                $table->addColumn($column, 'string', [
                    'notnull' => false,
                    'length' => 255
                ]);
            }
        }

        if (!$table->hasColumn('created')) {
            $table->addColumn('created', 'datetime', [
                'notnull' => false,
            ]);
        }

        if (!$table->hasColumn('updated')) {
            $table->addColumn('updated', 'datetime', [
                'notnull' => false,
            ]);
        }

        // ---------------------------------------
        // guitarsongbook_shots
        // ---------------------------------------

        if ($schema->hasTable('guitarsongbook_shots')) { // Don’t use table name longer than 23 characters. => ok!
            $table = $schema->getTable('guitarsongbook_shots');
        }
        else {
            $table = $schema->createTable('guitarsongbook_shots');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->setPrimaryKey(['id']);

            $table->addColumn('song_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addIndex(['song_id'], 'guitarsongbook_shots_index');

            $table->addColumn('index', 'integer', [
                'notnull' => true,
            ]);

            $table->addColumn('url', 'string', [
                'notnull' => true,
                'length' => 255
            ]);
        }

        if (!$table->hasColumn('text')) {
            $table->addColumn('text', 'string', [
                'notnull' => false,
                'length' => 255
            ]);
        }

		return $schema;
	}
}
