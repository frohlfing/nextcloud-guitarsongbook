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
use OCP\Files\NotFoundException;
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
     * @param string $name Folder
     * @NoAdminRequired
     * @NoCSRFRequired
     * @throws NotFoundException
     */
    public function load(string $name): StreamResponse
    {
        return $this->fileService->file($name);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function save(): DataResponse
    {
        try {
            $bytes = file_get_contents('php://input');
            $name = $this->fileService->saveRequestBodyAsFile($bytes);
        }
        catch (Exception $e) {
            return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
        }

        return new DataResponse($name, Http::STATUS_OK);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function upload(IRequest $request): DataResponse
    {
        try {
            $file = $request->getUploadedFile('file');
            $name = $this->fileService->saveUploadedFile($file);
        }
        catch (Exception $e) {
            return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
        }

        return new DataResponse($name, Http::STATUS_OK);
    }

//    /**
//     * @NoAdminRequired
//     * @NoCSRFRequired
//     */
//    public function rename(string $name, string $newName): DataResponse
//    {
//        try {
//            $name = $this->fileService->rename($name, $newName);
//        }
//        catch (Exception $e) {
//            return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
//        }
//
//        return new DataResponse($name, Http::STATUS_OK);
//    }

//    /**
//     * @NoAdminRequired
//     * @NoCSRFRequired
//     */
//    public function destroy(string $name): DataResponse
//    {
//        try {
//            $name = $this->fileService->delete($name);
//        }
//        catch (Exception $e) {
//            return new DataResponse($e->getMessage(), Http::STATUS_BAD_REQUEST);
//        }
//
//        return new DataResponse($name, Http::STATUS_OK);
//    }
}
