<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Service;

use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCA\GuitarSongbook\Db\Song;
use OCA\GuitarSongbook\Db\SongMapper;

class SongService
{
	private SongMapper $mapper;

	public function __construct(SongMapper $mapper)
    {
		$this->mapper = $mapper;
	}

    /**
     * @return list<Song>
     * @noinspection PhpUnused
     * @throws \OCP\DB\Exception
     */
	public function findAll(string $userId): array
    {
		return $this->mapper->findAll($userId);
	}

	/**
	 * @return never
	 */
	private function handleException(Exception $e)
    {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new SongNotFound($e->getMessage());
		} else {
			throw $e;
		}
	}

    /**
     * @throws SongNotFound
     */
    public function find(int $id, string $userId): Song
    {
		try {
			return $this->mapper->find($id, $userId);

			// in order to be able to plug in different storage backends like files
            // for instance it is a good idea to turn storage related exceptions
            // into service related exceptions so controllers and service users
            // have to deal with only one type of exception
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}

	public function create(string $title, string $content, string $userId): Song
    {
		$song = new Song();
		$song->setTitle($title);
		$song->setContent($content);
		$song->setUserId($userId);
		return $this->mapper->insert($song);
	}

	public function update(int $id, string $title, string $content, string $userId): Song
    {
		try {
			$song = $this->mapper->find($id, $userId);
			$song->setTitle($title);
			$song->setContent($content);
			return $this->mapper->update($song);
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}

    /**
     * @throws SongNotFound
     */
    public function delete(int $id, string $userId): Song
    {
		try {
			$song = $this->mapper->find($id, $userId);
			$this->mapper->delete($song);
			return $song;
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
