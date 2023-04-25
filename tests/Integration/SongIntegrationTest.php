<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Tests\Integration\Controller;

use OCP\AppFramework\App;
use OCP\AppFramework\Db\QBMapper;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

use OCA\GuitarSongbook\Db\Song;
use OCA\GuitarSongbook\Db\SongMapper;
use OCA\GuitarSongbook\Controller\SongController;

class NoteIntegrationTest extends TestCase
{
	private SongController $controller;
	private QBMapper $mapper;
	private string $userId = 'john';

	public function setUp(): void {
		$app = new App('guitarsongbook');
		$container = $app->getContainer();

		// only replace the user id
        /** @noinspection PhpDeprecationInspection */
        $container->registerService('userId', function () {
			return $this->userId;
		});

		// we do not care about the request but the controller needs it
        /** @noinspection PhpDeprecationInspection */
        $container->registerService(IRequest::class, function () {
			return $this->createMock(IRequest::class);
		});

        $this->controller = $container->get(SongController::class);
        $this->mapper = $container->get(SongMapper::class);
	}

	public function testUpdate(): void
    {
		// create a new song that should be updated
		$song = new Song();
		$song->setName('old_song');
		$song->setTitle('old_title');
		$song->setUserId($this->userId);

		$id = $this->mapper->insert($song)->getId();

		// fromRow does not set the fields as updated
		$updatedSong = Song::fromRow([
			'id' => $id,
			'user_id' => $this->userId
		]);
		$updatedSong->setTitle('title');
		$updatedSong->setName('name');

		$result = $this->controller->update($id, 'name', 'content');

		$this->assertEquals($updatedSong, $result->getData());

		// clean up
		$this->mapper->delete($result->getData());
	}
}
