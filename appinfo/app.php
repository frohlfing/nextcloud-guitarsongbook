<?php
/*
 * Copyright (c) Piotr Bator <prbator@gmail.com>
 *
 * This file is licensed under the Affero General Public License version 3
 * or later.
 *
 * See the COPYING-README file.
 *
 */
namespace OCA\GuitarTabPlayer\AppInfo;

use OCP\Util;

Util::addScript('guitarsongbook', 'alphatab/dist/alphaTab.min');

if(class_exists('\\OCP\\AppFramework\\Http\\EmptyContentSecurityPolicy')) {
    /** @noinspection PhpUndefinedClassInspection */
    $manager = \OC::$server->getContentSecurityPolicyManager();
    $csp = new \OCP\AppFramework\Http\EmptyContentSecurityPolicy();
    //$csp->addAllowedChildSrcDomain('\'self\'');
    //$csp->addAllowedWorkerSrcDomain('\'self\' blob: ;');
    $csp->addAllowedWorkerSrcDomain('\'self\'');
    $manager->addDefaultPolicy($csp);
}
