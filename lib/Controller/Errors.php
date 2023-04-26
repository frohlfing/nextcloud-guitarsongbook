<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\Controller;

use Closure;

use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCA\GuitarSongbook\Service\SongNotFound;

trait Errors
{
	protected function handleNotFound(Closure $callback): DataResponse
    {
		try {
			return new DataResponse($callback());
		}
        /** @noinspection PhpRedundantCatchClauseInspection */
        catch (SongNotFound $e) {
			return new DataResponse($e->getMessage(), Http::STATUS_NOT_FOUND);
		}
	}
}
