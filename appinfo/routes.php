<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\GuitarSongbook\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */

/**
 * Eine Resource hat folgende Routen:
 * 'routes' => [
 *    ['name' => 'page#index',   'url' => '/',           'verb' => 'GET'],
 *    ['name' => 'note#index',   'url' => '/notes',      'verb' => 'GET'],
 *    ['name' => 'note#show',    'url' => '/notes/{id}', 'verb' => 'GET'],
 *    ['name' => 'note#create',  'url' => '/notes',      'verb' => 'POST'],
 *    ['name' => 'note#update',  'url' => '/notes/{id}', 'verb' => 'PUT'],
 *    ['name' => 'note#destroy', 'url' => '/notes/{id}', 'verb' => 'DELETE']
 *  ]
 */

return [
	'resources' => [
		'note' => ['url' => '/notes'],
		'note_api' => ['url' => '/api/0.1/notes']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'note_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']]
	]
];