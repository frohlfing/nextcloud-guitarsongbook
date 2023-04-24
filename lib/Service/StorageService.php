<?php
namespace OCA\GuitarSongbook\Service;

use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;
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
     *
     * @throws NotPermittedException
     */
    private function getHomeFolder(): Folder
    {
        $user = $this->userSession->getUser();
        return $this->rootFolder->getUserFolder($user->getUID());
    }

    public function getSongsFolderName(): string
    {
        return '/Songs';
    }

    /**
     * @param string $folder The folder must exist in the song directory
     * @return string
     * @throws NotFoundException
     * @throws NotPermittedException
     */
    public function getFullPath(string $folder = ''): string
    {
        /** @noinspection PhpUndefinedClassInspection */
        $dataPath = OC::$SERVERROOT . '/data';
        $homeFolder = $this->getHomeFolder();
        $songsFolderName = $this->getSongsFolderName();

        return $dataPath . $homeFolder->get($songsFolderName . '/' . $folder)->getPath();
    }
}
