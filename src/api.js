import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

function handleError(e)
{
	const message = e.response ? e.response.data : e.message
	console.log(`API: ${message}`)
	throw new Error(e)
}

const songs = {
	/**
	 * Fetch the list of songs
	 *
	 * @return {Promise<any>} Array
	 */
	index: async function () {
		const response = await axios.get(generateUrl('/apps/guitarsongbook/songs'))
			.catch(e => {
				handleError(e)
			})
		return response.data
	},

	/**
	 * Get the Song entity by id
	 *
	 * @param {int} id
	 * @return {Promise<any>} Object
	 */
	show: async function (id) {
		const response = await axios.get(generateUrl('/apps/guitarsongbook/songs/' + + encodeURIComponent(id)))
			.catch(e => {
				handleError(e)
			})
		return response.data
	},

	/**
	 * Get the url for Guitar Pro file by id
	 *
	 * @param {int} id
	 * @return {String}
	 */
	urlFile: function (id) {
		return generateUrl('/apps/guitarsongbook/songs/' + encodeURIComponent(id) + '/file')
	},

	/**
	 * Create a new File and return the new Song entity
	 *
	 * (Variante 1: Datei serverseitig erstellen)
	 *
	 * @param {String} name Folder
	 * @return {Promise<any>} Object
	 */
	create: async function (name) {
		const response = await axios.post(generateUrl('/apps/guitarsongbook/songs'), {
			name: name
		}).catch(e => {
			handleError(e)
		})
		return response.data
	},

	/**
	 * Upload a music file and return the new Song entity
	 *
	 * Supported formats:
	 * .gp3, .gp4, .gp5: Guitar Pro 3-5 files which are a proprietary binary format from Arobas Music
	 * .gp: Guitar Pro 7 files which are a zip archive storing the music information as XML
	 * .gpx, .cap, .xml: Guitar Pro 6 files (.gpx), CapXML files (.cap) and MusicXML files (.xml)
	 * .txt: alphaTex
	 *
	 * @param {File} file selected file from <input type="file"> via event.target.files[0]
	 * @return {Promise<any>} Object
	 */
	upload: async function (file) {
		// load file into a alphaTab Score
		const buf = await file.arrayBuffer();
		const bytes = new Uint8Array(buf);
		const settings = new alphaTab.Settings();
		const score = alphaTab.importer.ScoreLoader.loadScoreFromBytes(bytes, settings);
		const filename = file.name.replace(/\.[^/.]+$/, '.gp');  // change file extension

		// convert the score to a GP7 file
		const exporter = new alphaTab.exporter.Gp7Exporter()
		const uint8arr = exporter.export(score, settings) // will return a Uint8Array of the file
		const blob = new Blob([uint8arr])

		// upload the file
		//const csrf = document.querySelector('meta[name="csrf-token"]')?.content || null;
		const response = await fetch(generateUrl('/apps/guitarsongbook/songs/upload'),{
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				//'X-CSRF-TOKEN': csrf,
				//'Authorization': 'Bearer ' + api_token,
				'Content-Disposition': 'attachment; filename="' + filename + '"',
			},
			body: blob,
		});
		if (!response.ok) {  // status ist nicht 200-299?
			const isJson = response.headers.get('content-type')?.includes('application/json')
			const message = isJson ? await response.json() : `An error has occured: ${response.status}`
			throw new Error(message)
		}
		return response.json()
	},

	/**
	 * Update the given Song
	 *
	 * @param {Object} song
	 * @return {Promise<any>} The updated Song
	 */
	update: async function (song) {
		const response = await axios.put(generateUrl(`/apps/guitarsongbook/songs/${song.id}`), song)
			.catch(e => {
				handleError(e)
			})
		return response.data
	},

	/**
	 * Delete the given Song
	 *
	 * @param {Object} song
	 * @return {Promise<any>} The deleted Song
	 */
	destroy: async function (song) {
		const response = await axios.delete(generateUrl(`/apps/guitarsongbook/songs/${song.id}`))
			.catch(e => {
				handleError(e)
			})
		return response.data
	},
}
export default {
	songs: songs,
}
