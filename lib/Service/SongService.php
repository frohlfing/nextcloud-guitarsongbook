<?php
namespace OCA\GuitarSongbook\Service;

use Exception;
use OCP\AppFramework\Http\StreamResponse;
use OCP\IL10N;

class SongService {

    private IL10N $l;
    private StorageService $storage;

    public function __construct(IL10N $l, StorageService $storage)
    {
        $this->l = $l;
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
//            throw new Exception($this->l->t('File already exists.'));
//        }

        if ($file['size'] > 500000) {
            throw new Exception($this->l->t('Sorry, your file is too large.'));
        }

        if (!move_uploaded_file($file['tmp_name'], $fullname)) {
            throw new Exception($this->l->t('There was an error uploading your file.'));
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
