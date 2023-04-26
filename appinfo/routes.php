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
 *    ['name' => 'song#index',   'url' => '/songs',      'verb' => 'GET'],
 *    ['name' => 'song#show',    'url' => '/songs/{id}', 'verb' => 'GET'],
 *    ['name' => 'song#create',  'url' => '/songs',      'verb' => 'POST'],
 *    ['name' => 'song#update',  'url' => '/songs/{id}', 'verb' => 'PUT'],
 *    ['name' => 'song#destroy', 'url' => '/songs/{id}', 'verb' => 'DELETE']
 *  ]
 */

return [
	'resources' => [
		'song'     => ['url' => '/songs'],
		'song_api' => ['url' => '/api/0.1/songs']
	],
	'routes' => [
        // PageController
		['name' => 'page#index',  'url' => '/',                'verb' => 'GET'],

        // FileController
		['name' => 'file#load',    'url' => '/files/{name}',    'verb' => 'GET'],
		['name' => 'file#save',    'url' => '/files',           'verb' => 'POST'],
		['name' => 'file#upload',  'url' => '/files/upload',    'verb' => 'POST'],
		['name' => 'file#destroy', 'url' => '/files/{name}',    'verb' => 'DELETE'],

        // SongApiController
		['name' => 'song_api#preflighted_cors', 'url' => '/api/0.1/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']], // für CORS erforderlich
	]
];
