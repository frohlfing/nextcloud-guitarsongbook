<?php
/** @noinspection PhpUnused */
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 * @method getSongId(): int
 * @method setSongId(int $songId): int
 * @method getIndex(): int
 * @method setIndex(int $index): void
 * @method getUrl(): string
 * @method setUrl(string $url): void
 * @method getText(): ?string
 * @method setText(?string $text): void
 */
class Shot extends Entity implements JsonSerializable
{
	protected int $song_id = 0;
    protected int $index = 0;
    protected string $url = '';
	protected ?string $text = null;

//    public function __construct()
//    {
//    }

	public function jsonSerialize(): array
    {
		return [
			'id' => $this->id,
			'song_id' => $this->song_id,
			'url' => $this->url,
			'text' => $this->text,
		];
	}
}
