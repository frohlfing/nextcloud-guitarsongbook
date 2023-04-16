<?php
namespace OCA\GuitarSongbook\Service;

use OCP\AppFramework\Http\StreamResponse;

class SongService {

    private StorageService $storage;

    public function __construct(StorageService $storage)
    {
        $this->storage = $storage;
    }

    public function getContent($song)
    {
        return file_get_contents($this->storage->getFullPath($song));
    }

    public function file($song): StreamResponse
    {
        return new StreamResponse($this->storage->getFullPath($song));
    }
}
