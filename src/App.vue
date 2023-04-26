<!--suppress CssUnresolvedCustomProperty -->
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
		<NcAppContent>
			<div class="controls-wrapper">
				<div class="location-wrapper">
					<h2 class="location">
            {{ t('guitarsongbook', 'New song') }}
					</h2>
				</div>
        <NcButton
            v-if="true"
            type="primary"
            :aria-label="t('guitarsongbook', 'Edit')"
            @click="">
          <template #icon>
            <PencilIcon :size="20" />
          </template>
          {{ t("guitarsongbook", "Edit") }}
        </NcButton>
        <NcButton
            type="primary"
            v-if="true"
            :aria-label="t('guitarsongbook', 'Save')"
            @click="">
          <template #icon>
            <LoadingIcon
                class="animation-rotate"
                v-if="false"
                :size="20"/>
            <CheckmarkIcon v-else :size="20" />
          </template>
          {{ t('guitarsongbook', 'Save') }}
        </NcButton>
        <NcActions
            class="overflow-menu"
            v-if="true"
            :force-menu="true">
          <NcActionButton
              class="action-button"
              v-if="true"
              :icon="loading ? 'icon-loading-small' : 'icon-history'"
              :aria-label="t('guitarsongbook', 'Reload')"
              @click="">
            {{ t("guitarsongbook", "Reload") }}
          </NcActionButton>
          <NcActionButton
              class="action-button"
              v-if="true"
              :aria-label="t('guitarsongbook', 'Print')"
              @click="">
            <template #icon="">
              <printer-icon :size="20" />
            </template>
            {{ t('guitarsongbook', 'Print') }}
          </NcActionButton>
          <NcActionButton
              icon="icon-delete"
              class="action-button"
              v-if="true"
              :aria-label="t('guitarsongbook', 'Delete')"
              @click="">
            {{ t('guitarsongbook', 'Delete') }}
          </NcActionButton>
          <NcActionButton
              class="action-button"
              v-if="true"
              :aria-label="t('guitarsongbook', 'Cancel')"
              @click="">
            {{ t('guitarsongbook', 'Cancel') }}
            <template #icon>
              <NcLoadingIcon v-if="false" :size="20"/>
              <eye-icon v-else :size="20" />
            </template>
          </NcActionButton>
        </NcActions>
			</div>
			<AppMain
          :song="currentSong"
          @saved="songUpdated"/>
		</NcAppContent>
	</NcContent>
</template>

<!--suppress ExceptionCaughtLocallyJS -->
<script>
import NcContent from '@nextcloud/vue/dist/Components/NcContent'
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew'
import NcActions from '@nextcloud/vue/dist/Components/NcActions'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton'
import NcActionInput from '@nextcloud/vue/dist/Components/NcActionInput'
import NcButton from '@nextcloud/vue/dist/Components/NcButton'
import AppMain from './components/AppMain'
import AppSettingsDialog from './components/AppSettingsDialog'
import FileSelect from './components/FileSelect'
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon"
import LoadingIcon from "vue-material-design-icons/Loading.vue"
import PlusIcon from 'vue-material-design-icons/Plus'
import ReloadIcon from 'vue-material-design-icons/Reload'
import CogIcon from 'vue-material-design-icons/Cog'
import PencilIcon from "vue-material-design-icons/Pencil.vue"
import CheckmarkIcon from "vue-material-design-icons/Check.vue"
import PrinterIcon from "vue-material-design-icons/Printer.vue"
import EyeIcon from "vue-material-design-icons/Eye.vue"
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
		NcAppContent,
    NcActions,
		NcActionButton,
		NcActionInput,
		NcButton,
    AppMain,
    AppSettingsDialog,
    FileSelect,
    NcLoadingIcon,
    LoadingIcon,
    PlusIcon,
    ReloadIcon,
    CogIcon,
    PrinterIcon,
    PencilIcon,
    CheckmarkIcon,
    EyeIcon,
	},
	data() {
		return {
			songs: [],
			currentSongId: null,
			loading: true,
      updating: false,
      filename: null,
      score: null,
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
    async saveScoreAsGP7(score, filename) {
      // create file
      const exporter = new alphaTab.exporter.Gp7Exporter();
      const settings = new alphaTab.Settings();
      const bytes = exporter.export(score, settings); // will return a Uint8Array
      const blob = new Blob([bytes]);

      // upload file
      //const csrf = document.querySelector('meta[name="csrf-token"]')?.content || null;
      const response = await fetch(generateUrl('/apps/guitarsongbook/files'),{
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
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const message = isJson ? await response.json() : `An error has occured: ${response.status}`;
        throw new Error(message);
      }
      const name = await response.json();

      // save the database entity
      const data = {
        name: name,
        title: score.title,
        artist: score.artist,
        subtitle: score.subtitle,
        album: score.album,
        words: score.words,
        music: score.music,
        copyright: score.copyright,
        transcriber: score.transcriber,
        notice: score.notice,
        instructions: score.instructions
      }
      const response2 = await axios.post(generateUrl('/apps/guitarsongbook/songs'), data)

      // return the new song entry
      return response2.data
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
        const api = new alphaTab.AlphaTabApi(document.createElement('div'), { useWorkers: false })
        api.tex(`\\title "${name}" .`);
        const song = await this.saveScoreAsGP7(api.score, api.score.title + '.gp')
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
        //await axios.delete(generateUrl(`/apps/guitarsongbook/files/${song.name}`))
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
      // this.$nextTick(() => {
      // 	this.$refs.name.focus()
      // })
    },
    // ---------------------------
    // Settings
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

/*App*/

#app-content > div {
  width: 100%;
  height: 100%;
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

/* AppControls */

div.controls-wrapper {
  --nc-button-size: 44px;
  --vertical-padding: 4px;
  position: sticky;
  z-index: 2;
  top: 0;
  display: flex;
  width: 100%;
  min-height: calc(44px + 2 * var(--vertical-padding));
  flex-direction: row;
  padding: var(--vertical-padding) 1rem var(--vertical-padding) calc(44px + 2 * var(--vertical-padding));
  border-bottom: 1px solid var(--color-border);
  background-color: var(--color-main-background);
  gap: 8px;
}

div.location-wrapper {
   display: flex;
   flex: 1;
   flex-direction: column;
   justify-content: center;
 }

h2.location {
  width: 100%;
  margin-bottom: 0;
  font-size: 1.2em;
  line-height: 1em;
  overflow-x: clip;
  overflow-y: visible;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
