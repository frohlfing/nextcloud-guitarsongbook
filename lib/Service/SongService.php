<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Service;

use DateTime;
use Exception;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCA\GuitarSongbook\Db\Song;
use OCA\GuitarSongbook\Db\SongMapper;
use OCA\GuitarSongbook\Db\ShotMapper;

class SongService
{
	private SongMapper $songMapper;
	private ShotMapper $shotMapper;

	public function __construct(SongMapper $songMapper, ShotMapper $shotMapper)
    {
		$this->songMapper = $songMapper;
		$this->shotMapper = $shotMapper;
	}

    /**
     * @param string $userId
     * @return array
     * @throws \OCP\DB\Exception
     */
	public function findAll(string $userId): array
    {
		return $this->songMapper->findAll($userId);
	}

    /**
     * @param Exception $e
     * @return never
     * @throws SongNotFound
     * @throws Exception
     */
	private function handleException(Exception $e)
    {
		if ($e instanceof DoesNotExistException ||
			$e instanceof MultipleObjectsReturnedException) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new SongNotFound($e->getMessage());
		}
        else {
			throw $e;
		}
	}

    /**
     * @param int $id
     * @param string $userId
     * @return Song
     * @throws SongNotFound
     */
    public function find(int $id, string $userId): Song
    {
		try {
			return $this->songMapper->find($id, $userId);

			// in order to be able to plug in different storage backends like files
            // for instance it is a good idea to turn storage related exceptions
            // into service related exceptions so controllers and service users
            // have to deal with only one type of exception
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}

    /**
     * @param string $name
     * @param string $title
     * @param string $userId
     * @return Song
     * @throws \OCP\DB\Exception
     */
	public function create(string $name, string $title, string $userId): Song
    {
        //$now = (new DateTime())->getTimestamp();
        $now = (new DateTime())->format('Y-m-d H:i:s');
        //$now = new DateTime();

		$song = new Song();
		$song->setName($name);
        $song->setUserId($userId);
		$song->setTitle($title);
        $song->setCreated($now);
        $song->setUpdated($now);
		return $this->songMapper->insert($song);
	}

    /**
     * @param int $id
     * @param string $name
     * @param string $title
     * @param string $userId
     * @return Song
     * @throws SongNotFound
     */
	public function update(int $id, string $name, string $title, string $userId): Song
    {
        //$now = (new DateTime())->getTimestamp();
        $now = (new DateTime())->format('Y-m-d H:i:s');
        //$now = new DateTime();

		try {
			$song = $this->songMapper->find($id, $userId);
			$song->setName($name);
			$song->setTitle($title);
            $song->setUpdated($now);
			return $this->songMapper->update($song);
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}

    /**
     * @param int $id
     * @param string $userId
     * @return Song
     * @throws SongNotFound
     */
    public function delete(int $id, string $userId): Song
    {
		try {
            // delete Shots
            $songs = $this->shotMapper->findAllOfSong($id, $userId);
            foreach ($songs as $song) {
                $this->shotMapper->delete($song);
            }
            // delete Songs
			$song = $this->songMapper->find($id, $userId);
			$this->songMapper->delete($song);
			return $song;
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
