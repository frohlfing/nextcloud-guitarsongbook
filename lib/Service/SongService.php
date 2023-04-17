<?php
namespace OCA\GuitarSongbook\Service;

use Exception;
use OCP\AppFramework\Http\StreamResponse;

class SongService {

    private StorageService $storage;

    public function __construct(StorageService $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @throws Exception
     */
    public function saveUploadedFile($file): string
    {
        $filename = basename($file['name']);
        $fullname = $this->storage->getFullPath() . '/' . $filename;

        // Validate
        $error = '';
//        if (file_exists($fullname)) {
//            throw new Exception('File already exists.');
//        }

        if ($file['size'] > 500000) {
            throw new Exception('Sorry, your file is too large.');
        }

        if (!move_uploaded_file($file['tmp_name'], $fullname)) {
            throw new Exception('There was an error uploading your file.');
        }

        return $filename;
    }

//    public function getContent($song)
//    {
//        return file_get_contents($this->storage->getFullPath($song));
//    }

    public function file($song): StreamResponse
    {
        return new StreamResponse($this->storage->getFullPath($song));
    }
}
