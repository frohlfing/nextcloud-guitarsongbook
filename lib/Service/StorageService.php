<?php
namespace OCA\GuitarSongbook\Service;

use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;

//use OCP\IUserSession;

class StorageService
{
    private IRootFolder $rootFolder;
    private Folder $homeFolder;

    /**
     * @throws NotPermittedException
     */
    public function __construct(IRootFolder $rootFolder, ?string $userId)
    {
        $this->rootFolder = $rootFolder;
        $this->homeFolder = $this->rootFolder->getUserFolder($userId);
    }

//    public function __construct(IRootFolder $rootFolder, IUserSession $userSession)
//    {
//        $this->rootFolder = $rootFolder;
//        $userId = $userSession->getUser()->getUID();
//        $this->homeFolder = $this->rootFolder->getUserFolder($userId);
//    }

    public function getSongsFolderName(): string
    {
        return '/Songs';
    }

    /**
     * @param string $folder The folder must exist in the song directory
     * @return string
     * @throws NotFoundException
     */
    public function getFullPath(string $folder = ''): string
    {
        /** @noinspection PhpUndefinedClassInspection */
        $dataPath = OC::$SERVERROOT . '/data';
        $songsFolderName = $this->getSongsFolderName();

        return $dataPath . $this->homeFolder->get($songsFolderName . '/' . $folder)->getPath();
    }
}
