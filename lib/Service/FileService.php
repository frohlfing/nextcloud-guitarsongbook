<?php /** @noinspection DuplicatedCode */

namespace OCA\GuitarSongbook\Service;

use Exception;
use InvalidArgumentException;
use OCP\AppFramework\Http\StreamResponse;
use OCP\Files\AlreadyExistsException;
use OCP\Files\NotFoundException;
use OCP\IL10N;
use ZipArchive;

class FileService {

    private IL10N $l;
    private StorageService $storage;

    public function __construct(IL10N $l, StorageService $storage)
    {
        $this->l = $l;
        $this->storage = $storage;
    }

    /**
     * Create a new Guitar Pro file.
     *
     * @param string $name
     * @return string
     * @throws NotFoundException
     * @throws Exception
     */
    public function saveEmptyFile(string $name): string
    {
        // validate
        if (empty($name)) {
            throw new InvalidArgumentException($this->l->t('Name required!'));
        }

        $emptyFile = realpath(__DIR__ . '/../../gp/empty.gp') ; // __DIR__ == ./guitarsongbook/lib/Service

        // create the folder
        $path = $this->storage->getFullPath() . '/' . $name;
        if (is_dir($path)) {
            throw new AlreadyExistsException($this->l->t('Folder %1$s already exists', [$name]));
        }
        if (!is_dir($path) && mkdir($path) === false) {
            throw new Exception($this->l->t('Unable to create the folder %1$s', [$name]));
        }
        if (copy($emptyFile, $path . '/song.gp') === false) {
            throw new Exception($this->l->t('Unable to save the file %1$s', [$name .'/song.gp']));
        }

        return $name;
    }

    /**
     * Save the raw data as Guitar Pro 7 file.
     *
     * @param mixed $blob Raw data from the file
     * @return string Filename
     * @throws NotFoundException
     * @throws Exception
     */
    public function saveBlobAsFile($blob, $filename): string
    {
        // validate filename
        if (empty($filename)) {
            throw new InvalidArgumentException($this->l->t('Filename required'));
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext !== 'gp') {
            throw new InvalidArgumentException($this->l->t('Guitar Pro 7 File expected'));
        }

        // validate the raw data
        if (!$blob) {
            throw new InvalidArgumentException($this->l->t('File required'));
        }
        $size = strlen($blob);
        $maxSize = 10 * 1024 * 1024; // 10 MB  config('upload.max_size.document')
        if ($size > $maxSize) {
            throw new InvalidArgumentException($this->l->t('File is too large.'));
        }

        // create the folder
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $path = $this->storage->getFullPath() . '/' . $name;
        if (is_dir($path)) {
            throw new AlreadyExistsException($this->l->t('Folder %1$s already exists', [$name]));
        }
        if (!is_dir($path) && mkdir($path) === false) {
            throw new Exception($this->l->t('Unable to create the folder %1$s', [$name]));
        }

        // save the file
        if (file_put_contents($path . '/song.gp', $blob, LOCK_EX) === false) {
            throw new Exception($this->l->t('Unable to save the file %1$s', [$name .'/song.gp']));
        }

        return $name;
    }

    /**
     * Rename the folder
     *
     * @param string $name
     * @param string $newName
     * @return string
     * @throws NotFoundException
     * @throws Exception
     */
    public function rename(string $name, string $newName): string
    {
        // todo wenn dateinamen nur casesensitive unerschiedlich sind, zwischenschritt einbauen

        // validate
        if (empty($name) || empty($newName)) {
            throw new InvalidArgumentException($this->l->t('Name required!'));
        }
        $path = $this->storage->getFullPath($name);
        $newPath = $this->storage->getFullPath() . '/' . $newName;
        if (is_dir($newPath)) {
            throw new AlreadyExistsException($this->l->t('Folder %1$s already exists', [$newName]));
        }

        // rename the folder
        if (rename($path, $newPath) === false) {
            throw new Exception($this->l->t('Unable to rename the folder %1$s to %2$s!', [$name, $newName]));
        }

        return $name;
    }

    /**
     * Remove the given folder if exists
     *
     * @param string $name
     * @return string
     * @throws NotFoundException
     * @throws Exception
     */
    public function destroy(string $name): string
    {
        // validate
        if (empty($name)) {
            throw new InvalidArgumentException($this->l->t('Name required!'));
        }
        $path = $this->storage->getFullPath($name);

        // remove the folder if exists
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                if (unlink($path . '/' . $file) === false) {
                    throw new Exception($this->l->t('Unable to delete the file %1$s', [$name . '/' . $file]));
                }
            }
            if (rmdir($path) === false) {
                throw new Exception($this->l->t('Unable to delete the folder %1$s', [$name]));
            }
        }

        return $name;
    }

    /**
     * Get the raw contents of the Guitar Pro file.
     *
     * @param string $name
     * @return StreamResponse
     * @throws NotFoundException
     */
    public function file(string $name): StreamResponse
    {
        return new StreamResponse($this->storage->getFullPath($name) . '/song.gp');
    }

    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function getInformation(string $name): object
    {
        $zip = new ZipArchive();
        if (!$zip->open($this->storage->getFullPath($name) . '/song.gp', ZipArchive::RDONLY)) {
            throw new Exception($this->l->t('Unable to open the file %1$s', [$name . '/song.gp']));

        }
        $version = $zip->getFromName('VERSION');
        if ($version !== '7.0') {
            throw new Exception($this->l->t('GP Version 7.0 expected but found %1$s', [$version]));
        }
        $text = $zip->getFromName('Content/score.gpif');
        if ($text === false) {
            throw new Exception($this->l->t('Could not find the score information from %1$s', [$name . '/song.gp']));
        }
        $xml = simplexml_load_string($text);
        if ($xml === false) {
            throw new Exception($this->l->t('Could not read the score information from %1$s', [$name . '/song.gp']));
        }

        $info = [];
        foreach ($xml->children() as $key => $child) {
            if ($key === 'Score') {
                foreach ($child->children() as $k => $v) {
                    if (in_array($k, ['Title', 'Artist', 'SubTitle', 'Album', 'Words', 'Music', 'Copyright', 'Tabber', 'Notices', 'Instructions'])) {
                        if ($k === 'Tabber') { $k = 'Transcriber'; } // == tab in AlphaTab 1.2.3
                        $info[strtolower($k)] = (string)$v;
                    }
                }
                break;
            }
        }

        return (object)$info;
    }
}
