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
            @select="importMusicFile"/>
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
        @updated="songUpdated"
        @deleted="songDeleted"/>
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
import { showError, showSuccess } from '@nextcloud/dialogs'
import api from '../api'

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
		currentSong() {
			if (this.currentSongId === null) {
				return null
			}
			return this.songs.find((song) => song.id === this.currentSongId) // Return the currently selected song object
		},
	},
	async mounted() {
		try {
			this.songs = await api.songs.index()  // Fetch list of songs
		}
    catch (e) {
			showError(t('guitarsongbook', 'Could not fetch the songbook'))
		}
		this.loading = false
	},
	methods: {
    // ---------------------------
    // Navigation
    // ---------------------------
    openSong(song) {
      this.currentSongId = song.id
    },
    async createSong() {
      this.updating = true
      try {
        const song = await api.songs.create(t('guitarsongbook', 'New Song'))
        this.songs.push(song)
        this.currentSongId = song.id
      }
      catch (e) {
        showError(t('guitarsongbook', 'Could not create the song: {message}', e))
      }
      this.updating = false
    },
    async importMusicFile(file) { // file selected file from <input type="file">
      this.updating = true
      try {
        const song = await api.songs.upload(file)
        this.songs.push(song)
        this.currentSongId = song.id
      }
      catch (e) {
        showError(t('guitarsongbook', 'Could not import the file: {message}', e))
      }
      this.updating = false
    },
		async deleteSong(song) {
      this.updating = true
			try {
				await api.songs.destroy(song)
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
    songUpdated(song) {
      console.log('App: songUpdated', song)
      const index = this.songs.findIndex((match) => match.id === song.id)
      this.$set(this.songs, index, song)
      this.currentSongId = song.id
    },
    songDeleted(song) {
      console.log('App: songDeleted', song)
      this.songs.splice(this.songs.indexOf(song), 1)
      if (this.currentSongId === song.id) {
        this.currentSongId = null
      }
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
