<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * You can test the API by running a GET request with curl:
 * curl -u user:password http://localhost/nextcloud/index.php/apps/guitarsongbook/api/0.1/notes
 * curl -u admin:12345abc http://localhost/nextcloud/index.php/apps/guitarsongbook/api/0.1/notes
 */

namespace OCA\GuitarSongbook\Controller;

use OCA\GuitarSongbook\AppInfo\Application;
use OCA\GuitarSongbook\Service\SongService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\DB\Exception;
use OCP\IRequest;

class SongApiController extends ApiController
{
	private SongService $service;
	private ?string $userId;

	use Errors;

	public function __construct(IRequest $request, SongService $service, ?string $userId)
    {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     * @throws Exception
     */
	public function index(): DataResponse
    {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse
    {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function create(string $title, string $content): DataResponse
    {
		return new DataResponse($this->service->create($title, $content,
			$this->userId));
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title, string $content): DataResponse
    {
		return $this->handleNotFound(function () use ($id, $title, $content) {
			return $this->service->update($id, $title, $content, $this->userId);
		});
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse
    {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}