<template>
	<!--
    SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
    SPDX-License-Identifier: AGPL-3.0-or-later
    -->
	<NcContent app-name="guitarsongbook">
		<NcAppNavigation>
      <template #list>
        <NcAppNavigationNew
            button-id="new-guitarsongbook-button"
            button-class="icon-add"
            :text="t('guitarsongbook', 'Create song')"
            :disabled="loading || updating"
            @click="createSong">
          <template #icon>
            <PlusIcon :size="20" />
          </template>
        </NcAppNavigationNew>
        <FileSelect
            :text="t('guitarsongbook', 'Import Music File')"
            accept=".gp3, .gp4, .gp5, .gpx, .gp, .cap, .xml, .txt"
            @change="importMusicFile"/>
        <ul>
          <NcAppNavigationItem
            v-for="song in songs"
            :key="song.id"
            :title="song.name ? song.name : t('guitarsongbook', 'New song')"
            :class="{active: currentSongId === song.id}"
            :deacitve="loading || updating"
            @click="openSong(song)">
            <template #actions>
              <NcActionButton
                icon="icon-delete"
                @click="deleteSong(song)">
                {{ t('guitarsongbook', 'Delete') }}
              </NcActionButton>
            </template>
          </NcAppNavigationItem>
        </ul>
      </template>
      <template #footer>
        <NcAppNavigationNew
            :text="t('guitarsongbook', 'Songbook settings')"
            @click="openSettingsDialog">
          <template #icon>
            <CogIcon :size="20" />
          </template>
        </NcAppNavigationNew>
        <AppSettingsDialog :open="settingsOpen" @close="closeSettingsDialog"/>
      </template>
    </NcAppNavigation>
    <AppContent
        :song="currentSong"
        @saved="songUpdated"/>
	</NcContent>
</template>

<script>
import NcContent from '@nextcloud/vue/dist/Components/NcContent'
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton'
import AppContent from './AppContent'
import AppSettingsDialog from './AppSettingsDialog'
import FileSelect from './FileSelect'
import PlusIcon from 'vue-material-design-icons/Plus'
import CogIcon from 'vue-material-design-icons/Cog'
import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		NcContent,
		NcAppNavigation,
		NcAppNavigationItem,
		NcAppNavigationNew,
		NcActionButton,
    AppContent,
    AppSettingsDialog,
    FileSelect,
    PlusIcon,
    CogIcon,
	},
	data() {
		return {
			songs: [],
			currentSongId: null,
			loading: true,
      updating: false,
      settingsOpen: false,
		}
	},
	computed: {
		/**
		 * Return the currently selected song object
		 *
		 * @return {object | null}
		 */
		currentSong() {
			if (this.currentSongId === null) {
				return null
			}
			return this.songs.find((song) => song.id === this.currentSongId)
		},
	},
	/**
	 * Fetch list of songs when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/guitarsongbook/songs'))
			this.songs = response.data
		}
    catch (e) {
      console.log(e.response ? e.response.data : e.message)
			showError(t('guitarsongbook', 'Could not fetch the songbook'))
		}
		this.loading = false
	},
	methods: {
    // ---------------------------
    // Navigation
    // ---------------------------
    async saveScoreAsGP7(score, filename) {
      // create file
      const exporter = new alphaTab.exporter.Gp7Exporter()
      const settings = new alphaTab.Settings()
      const bytes = exporter.export(score, settings) // will return a Uint8Array
      const blob = new Blob([bytes])

      // upload file and create a database entry
      //const csrf = document.querySelector('meta[name="csrf-token"]')?.content || null;
      const response = await fetch(generateUrl('/apps/guitarsongbook/songs/file'),{
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
      // return the new database entry
      return response.json()
    },
    /**
     * Create a new song by sending the information to the server
     *
     * __@param {object} song Song object
     */
    async createSong() {
      this.updating = true
      const name = t('guitarsongbook', 'New Song')
      try {
        // Variante 1 (Datei clientseitig erstellen):
        //const api = new alphaTab.AlphaTabApi(document.createElement('div'), { useWorkers: false })
        //api.tex(`\\title "${name}" .`);
        //const song = await this.saveScoreAsGP7(api.score, api.score.title + '.gp')
        //this.songs.push(song)
        //this.currentSongId = song.id

        // Variante 2 (Datei serverseitig erstllen):
        const response = await axios.post(generateUrl('/apps/guitarsongbook/songs'), { name: name })
        const song = response.data
        this.songs.push(song)
        this.currentSongId = song.id
      }
      catch (e) {
        console.log(e.response ? e.response.data : e.message)
        showError(t('guitarsongbook', 'Could not create the song: {message}', e))
      }
      this.updating = false
    },
    /**
     * @param file selected file from <input type="file">
     */
    async importMusicFile(file) {
      this.updating = true
      try {
        const buf = await file.arrayBuffer();
        const bytes = new Uint8Array(buf);
        const settings = new alphaTab.Settings();
        const score = alphaTab.importer.ScoreLoader.loadScoreFromBytes(bytes, settings);
        const filename = file.name.replace(/\.[^/.]+$/, '.gp');  // change file extension
        const song = await this.saveScoreAsGP7(score, filename)
        this.songs.push(song)
        this.currentSongId = song.id
      }
      catch (e) {
        console.log(e.response ? e.response.data : e.message)
        showError(t('guitarsongbook', 'Could not import the file: {message}', e))
      }
      this.updating = false
    },
		/**
		 * Delete a song, remove it from the frontend and show a hint
		 *
		 * @param {object} song Song object
		 */
		async deleteSong(song) {
      this.updating = true
			try {
				await axios.delete(generateUrl(`/apps/guitarsongbook/songs/${song.id}`))
				this.songs.splice(this.songs.indexOf(song), 1)
        if (this.currentSongId === song.id) {
					this.currentSongId = null
				}
				showSuccess(t('guitarsongbook', 'Song deleted'))
			}
      catch (e) {
        console.log(e.response ? e.response.data : e.message)
				showError(t('guitarsongbook', 'Could not delete the song'))
			}
      this.updating = false
		},
    // ---------------------------
    // AppContent
    // ---------------------------
    /**
     * Refresh the updated song in the list
     *
     * @param song
     */
    songUpdated(song) {
      const index = this.songs.findIndex((match) => match.id === song.id)
      this.$set(this.songs, index, song)
      this.currentSongId = song.id
    },
    /**
     * Create a new song and focus the song content field automatically
     *
     * @param {object} song Song object
     */
    openSong(song) {
      this.currentSongId = song.id
    },
    // ---------------------------
    // AppSettingsDialog
    // ---------------------------
    openSettingsDialog() {
      this.settingsOpen = true
    },
    closeSettingsDialog() {
      this.settingsOpen = false
    },
	},
}
</script>

<style scoped>
#app-content > div {
  width: 100%;
  height: 100%;
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}
</style>
