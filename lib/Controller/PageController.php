<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Controller;

use Exception;
use OCA\GuitarSongbook\AppInfo\Application;
use OCA\GuitarSongbook\Service\SongService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Util;

class PageController extends Controller {
    private SongService $songService;

	public function __construct(IRequest $request, SongService $songService) {
		parent::__construct(Application::APP_ID, $request);
        $this->songService = $songService;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse {
		Util::addScript(Application::APP_ID, 'guitarsongbook-main');

		return new TemplateResponse(Application::APP_ID, 'main');
	}

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function load($filename): StreamResponse
    {
        return $this->songService->file($filename);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function upload(IRequest $request): DataResponse
    {
        try {
            $file = $request->getUploadedFile('file');
            $song = $this->songService->saveUploadedFile($file);
        } catch (Exception $e) {
            return new DataResponse(['message' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }

        return new DataResponse($song, Http::STATUS_OK);
    }
}
