<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 * @method getName(): string
 * @method setName(string $name): void
 * @method getUserId(): string
 * @method setUserId(string $userId): void
 * @method getTitle(): ?string
 * @method setTitle(?string $title): void
 * @method getArtist(): ?string
 * @method setArtist(?string $artist): void
 * @method getSubtitle(): ?string
 * @method setSubtitle(?string $subtitle): void
 * @method getAlbum(): ?string
 * @method setAlbum(?string $album): void
 * @method getWords(): ?string
 * @method setWords(?string $words): void
 * @method getMusic(): ?string
 * @method setMusic(?string $music): void
 * @method getCopyright(): ?string
 * @method setCopyright(?string $copyright): void
 * @method getTranscriber(): ?string
 * @method setTranscriber(?string $transcriber): void
 * @method getNotice(): ?string
 * @method setNotice(?string $notice): void
 * @method getInstructions(): ?string
 * @method setInstructions(?string $instructions): void
 * @method getCreated(): string
 * @method setCreated(string $created): void
 * @method getUpdated(): string
 * @method setUpdated(string $updated): void
 */
class Song extends Entity implements JsonSerializable
{
	protected string $name = '';
    protected string $userId = '';
	protected ?string $title = null;
	protected ?string $artist = null;
	protected ?string $subtitle = null;
	protected ?string $album = null;
	protected ?string $words = null;
	protected ?string $music = null;
	protected ?string $copyright = null;
	protected ?string $transcriber = null;
	protected ?string $notice = null;
	protected ?string $instructions = null;
	protected string $created = '';
	protected string $updated = '';

//    public function __construct()
//    {
//        $this->created = new DateTimeImmutable();
//        $this->updated = new DateTimeImmutable();
//    }

//    todo so wird nichts geschrieben :(
//         es muss auch unbedingt string sein, und nicht ?string, erst recht nicht DateTime oder DateTimeImmutable
//         (sonst gibt es Excdeptions!)
//
//    public function getCreated(): DateTime
//    {
//        return DateTime::createFromFormat('Y-m-d H:i:s', $this->created);
//    }
//
//    public function setCreated(DateTime $dateTime)
//    {
//        $this->created = $dateTime->format('Y-m-d H:i:s');
//    }
//
//    public function getUpdated(): DateTime
//    {
//        return DateTime::createFromFormat('Y-m-d H:i:s', $this->updated);
//    }
//
//    public function setUpdated(DateTime $dateTime)
//    {
//        $this->updated = $dateTime->format('Y-m-d H:i:s');
//    }

	public function jsonSerialize(): array
    {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'title' => $this->title,
            'artist' => $this->artist,
            'subtitle' => $this->subtitle,
            'album' => $this->album,
            'words' => $this->words,
            'music' => $this->music,
            'copyright' => $this->copyright,
            'transcriber' => $this->transcriber,
            'notice' => $this->notice,
            'instructions' => $this->instructions,
            'created' => $this->created,
            'updated' => $this->created,
		];
	}
}
