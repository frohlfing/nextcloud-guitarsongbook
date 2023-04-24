<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\GuitarSongbook\AppInfo;

use OCP\AppFramework\App;

class Application extends App
{
	public const APP_ID = 'guitarsongbook';

	public function __construct()
    {
		parent::__construct(self::APP_ID);
	}
}
