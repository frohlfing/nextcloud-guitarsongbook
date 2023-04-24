<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Controller;

use Exception;
use OCA\GuitarSongbook\AppInfo\Application;
use OCA\GuitarSongbook\Service\FileService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\IRequest;

class FileController extends Controller
{
    private FileService $fileService;

	public function __construct(IRequest $request, FileService $fileService)
    {
		parent::__construct(Application::APP_ID, $request);
        $this->fileService = $fileService;
	}

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function load($filename): StreamResponse
    {
        return $this->fileService->file($filename);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function upload(IRequest $request): DataResponse
    {
        try {
            $file = $request->getUploadedFile('file');
            $song = $this->fileService->saveUploadedFile($file);
        }
        catch (Exception $e) {
            return new DataResponse(['message' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
        }

        return new DataResponse($song, Http::STATUS_OK);
    }
}
