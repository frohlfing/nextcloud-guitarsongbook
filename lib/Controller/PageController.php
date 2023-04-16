<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Controller;

use OCA\GuitarSongbook\AppInfo\Application;
use OCA\GuitarSongbook\Service\SongService;
use OCP\AppFramework\Controller;
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
}
