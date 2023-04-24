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
 * @method getUid(): string
 * @method setUid(string $uid): void
 * @method getTitle(): string
 * @method setTitle(string $title): void
 * @method getArtist(): string
 * @method setArtist(string $artist): void
 * @method getSubtitle(): string
 * @method setSubtitle(string $subtitle): void
 * @method getAlbum(): string
 * @method setAlbum(string $album): void
 * @method getWords(): string
 * @method setWords(string $words): void
 * @method getMusic(): string
 * @method setMusic(string $music): void
 * @method getCopyright(): string
 * @method setCopyright(string $copyright): void
 * @method getTranscriber(): string
 * @method setTranscriber(string $transcriber): void
 * @method getNotice(): string
 * @method setNotice(string $notice): void
 * @method getInstructions(): string
 * @method setInstructions(string $instructions): void
 * @method getCreated(): DateTime
 * @method setCreated(DateTime $created): void
 * @method getUpdated(): DateTime
 * @method setUpdated(DateTime $updated): void
 */
class Song extends Entity implements JsonSerializable
{
	protected string $name;
    protected string $uid;
	protected string $title;
	protected string $artist;
	protected string $subtitle;
	protected string $album;
	protected string $words;
	protected string $music;
	protected string $copyright;
	protected string $transcriber;
	protected string $notice;
	protected string $instructions;
	protected DateTime $created;
	protected DateTime $updated;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}
