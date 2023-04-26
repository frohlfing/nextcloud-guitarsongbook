<?php /** @noinspection DuplicatedCode */

namespace OCA\GuitarSongbook\Service;

use Exception;
use InvalidArgumentException;
use OCP\AppFramework\Http\StreamResponse;
use OCP\Files\AlreadyExistsException;
use OCP\Files\NotFoundException;
use OCP\IL10N;

class FileService {

    private IL10N $l;
    private StorageService $storage;

    public function __construct(IL10N $l, StorageService $storage)
    {
        $this->l = $l;
        $this->storage = $storage;
    }

    /**
     * Save the raw data from the request body as a Guitar Pro file.
     *
     * @param mixed $bytes Raw data from the request body (php://input)
     * @return string Folder
     * @throws NotFoundException
     * @throws Exception
     */
    public function saveRequestBodyAsFile($bytes): string
    {
        // get the file name from the Content Disposition Header
        $dispo = $_SERVER['HTTP_CONTENT_DISPOSITION'] ?: '';
        $i = strpos($dispo, 'filename=');
        $filename = $i !== false ? trim(substr($dispo, $i + 9), ' "\'') : '';
        if (empty($filename)) {
            throw new InvalidArgumentException($this->l->t('Filename required'));
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext !== 'gp') {
            throw new InvalidArgumentException($this->l->t('Guitar Pro 7 File expected'));
        }

        // validate the raw data
        if (!$bytes) {
            throw new InvalidArgumentException($this->l->t('File required'));
        }
        $size = strlen($bytes);
        $maxSize = 10 * 1024 * 1024; // 10 MB  config('upload.max_size.document')
        if ($size > $maxSize) {
            throw new InvalidArgumentException($this->l->t('File is too large.'));
        }

        // create the folder
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $path = $this->storage->getFullPath() . '/' . $basename;
        //if (is_dir($path)) { // todo hochzählen
            //throw new AlreadyExistsException($this->l->t('File already exists'));
        //}
        if (!is_dir($path) && mkdir($path) === false) {
            throw new Exception($this->l->t('Unable to create the folder'));
        }

        // save the file
        if (file_put_contents($path . '/song.gp', $bytes, LOCK_EX) === false) {
            throw new Exception($this->l->t('Unable to save the file'));
        }

        return $basename;
    }

    /**
     * Save the Guitar Pro file of the uploaded form
     *
     * @param mixed $file File of the uploaded form
     * @return string Folder
     * @throws NotFoundException
     * @throws Exception
     */
    public function saveUploadedFile($file): string
    {
        // validate the file
        $size = $file['size'];
        if (!$size) {
            throw new InvalidArgumentException($this->l->t('File required'));
        }
        $maxSize = 10 * 1024 * 1024; // 10 MB  config('upload.max_size.document')
        if ($size > $maxSize) {
            throw new InvalidArgumentException($this->l->t('File is too large.'));
        }
        $filename = basename($file['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if ($ext !== 'gp') {
            throw new InvalidArgumentException($this->l->t('Guitar Pro 7 File expected'));
        }

        // create the folder
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        $path = $this->storage->getFullPath() . '/' . $basename;
        if (is_dir($path)) {
            throw new AlreadyExistsException($this->l->t('File already exists'));
        }
        if (!is_dir($path) && mkdir($path) === false) { // todo hochzählen
            throw new Exception($this->l->t('Unable to create the folder'));
        }

        // save the file
        if (!move_uploaded_file($file['tmp_name'], $path . '/song.gp')) {
            throw new Exception($this->l->t('Unable to save the file'));
        }

        return $basename;
    }

    /**
     * Get the raw contents of the Guitar Pro file.
     *
     * @param string $name
     * @return string
     * @throws NotFoundException
     */
    public function delete(string $name): string
    {
        // validate
        if (empty($name)) {
            throw new InvalidArgumentException($this->l->t('Name required!'));
        }
        $path = $this->storage->getFullPath($name);

        // remove the folder
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            if (unlink($path . '/' . $file) === false) {
                throw new Exception($this->l->t('Unable to delete the file %1$s!', [$file]));
            }
        }
        if (rmdir($path) === false) {
            throw new Exception($this->l->t('Unable to delete the folder %1$s!', [$name]));
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
}
