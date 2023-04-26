<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Controller;

use OCA\GuitarSongbook\AppInfo\Application;
use OCA\GuitarSongbook\Service\SongNotFound;
use OCA\GuitarSongbook\Service\SongService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\DB\Exception;
use OCP\Files\NotFoundException;
use OCP\IRequest;

class SongController extends Controller
{
	private SongService $songService;
	private ?string $userId;

	use Errors;

	public function __construct(IRequest $request, SongService $songService, ?string $userId)
    {
		parent::__construct(Application::APP_ID, $request);
		$this->songService = $songService;
		$this->userId = $userId;
	}

    /**
     * @return DataResponse
     * @throws Exception
     * @NoAdminRequired
     */
	public function index(): DataResponse
    {
		return new DataResponse($this->songService->findAll($this->userId));
	}

    /**
     * @param int $id
     * @return DataResponse
     * @NoAdminRequired
     */
	public function show(int $id): DataResponse
    {
		return $this->handleNotFound(function () use ($id) {
			return $this->songService->find($id, $this->userId);
		});
	}

    /**
     * @param int $id
     * @return StreamResponse
     * @throws NotFoundException
     * @throws SongNotFound
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function file(int $id): StreamResponse
    {
        return $this->songService->file($id, $this->userId);
    }

    /**
     * @param string $name
     * @return DataResponse
     * @throws Exception
     * @throws NotFoundException
     * @NoAdminRequired
     * @NoCSRFRequired
     */
	public function create(string $name): DataResponse
    {
		return new DataResponse($this->songService->create($name, $this->userId));
	}

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @throws NotFoundException
     */
    public function save(): DataResponse
    {
        try {
            $bytes = file_get_contents('php://input');
            $song = $this->songService->saveRequestBodyAsFile($bytes, $this->userId);
        }
        catch (Exception $e) {
            return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
        }

        return new DataResponse($song);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @throws NotFoundException
     * @noinspection PhpUnused
     */
    public function upload(IRequest $request): DataResponse
    {
        try {
            $file = $request->getUploadedFile('file');
            $song = $this->songService->saveUploadedFile($file, $this->userId);
        }
        catch (Exception $e) {
            return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
        }

        return new DataResponse($song);
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $title
     * @param string $artist
     * @param string $subtitle
     * @param string $album
     * @param string $words
     * @param string $music
     * @param string $copyright
     * @param string $transcriber
     * @param string $notices
     * @param string $instructions
     * @return DataResponse
     * @NoAdminRequired
     */
	public function update(int $id, string $name, string $title, string $artist, string $subtitle, string $album, string $words, string $music, string $copyright, string $transcriber, string $notices, string $instructions): DataResponse
    {
		return $this->handleNotFound(function () use ($id, $name, $title, $artist, $subtitle, $album, $words, $music, $copyright, $transcriber, $notices, $instructions) {
			return $this->songService->update($id, $name, $title, $artist, $subtitle, $album, $words, $music, $copyright, $transcriber, $notices, $instructions, $this->userId);
		});
	}

    /**
     * @param int $id
     * @return DataResponse
     * @NoAdminRequired
     */
	public function destroy(int $id): DataResponse
    {
		return $this->handleNotFound(function () use ($id) {
			return $this->songService->delete($id, $this->userId);
		});
	}
}
