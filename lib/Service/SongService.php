<?php /** @noinspection DuplicatedCode */
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
use OCP\AppFramework\Http\StreamResponse;
use OCP\Files\NotFoundException;

class SongService
{
	private SongMapper $songMapper;
    private FileService $fileService;

    public function __construct(SongMapper $songMapper, FileService $fileService)
    {
        $this->songMapper = $songMapper;
        $this->fileService = $fileService;
    }

    /**
     * Fetch the list of songs
     *
     * @param string $userId
     * @return array
     * @throws \OCP\DB\Exception
     */
	public function findAll(string $userId): array
    {
		return $this->songMapper->findAll($userId);
	}

    /**
     * In order to be able to plug in different storage backends like files for instance it is a good idea to turn
     * storage related exceptions into service related exceptions so controllers and service users have to deal with
     * only one type of exception.
     *
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
            // \OC::$server->get(LoggerInterface::class)->warning('Error while opening archive '.$source, ['app' => 'files_archive']);
			throw $e;
		}
	}

    /**
     * Get the Song entity by id
     *
     * @param int $id
     * @param string $userId
     * @return Song
     * @throws SongNotFound
     */
    public function find(int $id, string $userId): Song
    {
		try {
			return $this->songMapper->find($id, $userId);
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}

    /**
     * Get the raw contents of the Guitar Pro file.
     *
     * @param int $id
     * @param string $userId
     * @return StreamResponse
     * @throws SongNotFound
     * @throws NotFoundException
     */
    public function file(int $id, string $userId): StreamResponse
    {
        $song = $this->find($id, $userId);
        return $this->fileService->file($song->getName());
    }

    /**
     * @param string $name
     * @param object $info
     * @param string $userId
     * @return Song
     * @throws \OCP\DB\Exception
     */
    private function insert(string $name, object $info, string $userId): Song
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

        $song = new Song();
        $song->setName($name);
        $song->setUserId($userId);
        $song->setTitle($info->title);
        $song->setArtist($info->artist);
        $song->setSubtitle($info->subtitle);
        $song->setAlbum($info->album);
        $song->setWords($info->words);
        $song->setMusic($info->music);
        $song->setCopyright($info->copyright);
        $song->setTranscriber($info->transcriber);
        $song->setNotices($info->notices);
        $song->setInstructions($info->instructions);
        $song->setCreated($now);
        $song->setUpdated($now);

        return $this->songMapper->insert($song);
    }

    /**
     * Create a new Song
     *
     * @param string $name
     * @param string $userId
     * @return Song
     * @throws NotFoundException
     * @throws \OCP\DB\Exception
     */
	public function create(string $name, string $userId): Song
    {
        $name = $this->fileService->saveEmptyFile($name);
        $info = $this->fileService->getInformation($name);
        return $this->insert($name, $info, $userId);
	}

    /**
     * Create a new Song with the GP7 raw data
     *
     * @param mixed $blob Raw data from the file
     * @param string $filename
     * @param string $userId
     * @return Song
     * @throws NotFoundException
     * @throws \OCP\DB\Exception
     */
    public function saveBlobAsFile($blob, string $filename, string $userId): Song
    {
        $name = $this->fileService->saveBlobAsFile($blob, $filename);
        $info = $this->fileService->getInformation($name);
        return $this->insert($name, $info, $userId);
    }

    /**
     * Update the Song
     *
     * @param int $id
     * @param string $name
     * @param string|null $title
     * @param string|null $artist
     * @param string|null $subtitle
     * @param string|null $album
     * @param string|null $words
     * @param string|null $music
     * @param string|null $copyright
     * @param string|null $transcriber
     * @param string|null $notices
     * @param string|null $instructions
     * @param string $userId
     * @return Song
     * @throws SongNotFound
     */
	public function update(int $id, string $name, ?string $title, ?string $artist, ?string $subtitle, ?string $album, ?string $words, ?string $music, ?string $copyright, ?string $transcriber, ?string $notices, ?string $instructions, string $userId): Song
    {
        $now = (new DateTime())->format('Y-m-d H:i:s');

		try {
			$song = $this->songMapper->find($id, $userId);

            if ($song->getName() !== $name) {
                $this->fileService->rename($song->getName(), $name);
            }

            // Todo File aktualisieren

			$song->setName($name);
			$song->setTitle($title);
            $song->setArtist($artist);
            $song->setSubtitle($subtitle);
            $song->setAlbum($album);
            $song->setWords($words);
            $song->setMusic($music);
            $song->setCopyright($copyright);
            $song->setTranscriber($transcriber);
            $song->setNotices($notices);
            $song->setInstructions($instructions);
            $song->setUpdated($now);

			return $this->songMapper->update($song);
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}

    /**
     * Delete the Song
     *
     * @param int $id
     * @param string $userId
     * @return Song
     * @throws SongNotFound
     */
    public function destroy(int $id, string $userId): Song
    {
		try {
			$song = $this->songMapper->find($id, $userId);
            $this->fileService->destroy($song->getName());
			$this->songMapper->delete($song);

			return $song;
		}
        catch (Exception $e) {
			$this->handleException($e);
		}
	}
}
