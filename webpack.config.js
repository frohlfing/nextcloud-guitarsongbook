// SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
// SPDX-License-Identifier: AGPL-3.0-or-later
const webpackConfig = require('@nextcloud/webpack-vue-config')
// const CopyPlugin = require('copy-webpack-plugin')
//
// webpackConfig.plugins = [
// 	new CopyPlugin({
// 		patterns: [
// 			{ from: 'node_modules/@coderline/alphatab/dist/alphaTab.min.js', to: 'alphatab/alphaTab.min.js' },
// 			{ from: 'node_modules/@coderline/alphatab/dist/font', to: 'alphatab/font' },
// 		],
// 	}),
// ]

module.exports = webpackConfig
