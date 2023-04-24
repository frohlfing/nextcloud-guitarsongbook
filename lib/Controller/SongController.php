<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Controller;

use OCA\GuitarSongbook\AppInfo\Application;
use OCA\GuitarSongbook\Service\SongService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\DB\Exception;
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
     * @NoAdminRequired
     * @throws Exception
     */
	public function index(): DataResponse
    {
		return new DataResponse($this->songService->findAll($this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse
    {
		return $this->handleNotFound(function () use ($id) {
			return $this->songService->find($id, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $title, string $content): DataResponse
    {
		return new DataResponse($this->songService->create($title, $content, $this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title, string $content): DataResponse
    {
		return $this->handleNotFound(function () use ($id, $title, $content) {
			return $this->songService->update($id, $title, $content, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse
    {
		return $this->handleNotFound(function () use ($id) {
			return $this->songService->delete($id, $this->userId);
		});
	}
}
