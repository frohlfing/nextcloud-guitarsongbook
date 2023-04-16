<?php
namespace OCA\GuitarSongbook\Service;

use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\IUserSession;

class StorageService {

    private IUserSession $userSession;
    private IRootFolder $rootFolder;

    public function __construct(IUserSession $userSession, IRootFolder $rootFolder)
    {
        $this->userSession = $userSession;
        $this->rootFolder = $rootFolder;
    }

    /**
     * Get the home folder name of the current user.
     * e.g.: /admin/files
     */
    private function getHomeFolder(): Folder
    {
        $user = $this->userSession->getUser();
        return $this->rootFolder->getUserFolder($user->getUID());
    }

    public function getSubPath(): string
    {
        return '/Songs';
    }

    public function getFullPath(string $song): string
    {
        /** @noinspection PhpUndefinedClassInspection */
        $dataPath = \OC::$SERVERROOT . '/data';
        $homeFolder = $this->getHomeFolder();
        $subPath = $this->getSubPath();

        return $dataPath . $homeFolder->get($subPath . '/' . $song)->getPath();
    }
}
