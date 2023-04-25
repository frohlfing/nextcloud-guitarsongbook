<!--suppress CssUnresolvedCustomProperty -->
<template>
	<!--
    SPDX-FileCopyrightText: Frank Rohlfing <mail@frank-rohlfing.de>
    SPDX-License-Identifier: AGPL-3.0-or-later
    -->
	<NcContent app-name="guitarsongbook">
		<NcAppNavigation>
      <template #list>
        <NcAppNavigationNew v-if="!loading"
            :text="t('guitarsongbook', 'Create song')"
            :disabled="false"
            button-id="new-guitarsongbook-button"
            button-class="icon-add"
            @click="newSong">
          <template #icon>
            <PlusIcon :size="20" />
          </template>
        </NcAppNavigationNew>
        <FileUpload
            :text="t('guitarsongbook', 'Upload Guitar Pro File')"
            accept=".gp3, .gp4, .gp5, .gpx, .gp, .cap, .xml, .txt"
            @uploaded="fileUploaded"/>
        <ul>
          <NcAppNavigationItem v-for="song in songs"
            :key="song.id"
            :title="song.name ? song.name : t('guitarsongbook', 'New song')"
            :class="{active: currentSongId === song.id}"
            @click="openSong(song)">
            <template #actions>
              <NcActionButton v-if="song.id === -1"
                icon="icon-close"
                @click="cancelNewSong(song)">
                {{ t('guitarsongbook', 'Discard') }}
              </NcActionButton>
              <NcActionButton v-else
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
              :icon="false ? 'icon-loading-small' : 'icon-history'"
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
			<div class="main-wrapper">

        <div class="at-track-list"></div>

				<AlphaTab
            :filename="filename"
            :tex="alphaTex"
            @score-loaded="scoreLoaded"
        />
				<div v-if="currentSong">
					<input ref="name" type="text"
              v-model="currentSong.name"
						  :disabled="updating">
					<input type="text"
              v-model="currentSong.title"
              :disabled="updating" />
					<input type="button" class="primary"
						  :value="t('guitarsongbook', 'Save')"
						  :disabled="updating || !savePossible"
						  @click="saveSong">
				</div>
				<div v-else id="emptycontent">
					<div class="icon-file" />
					<h2>
						{{
							t('guitarsongbook', 'Create a song to get started!') }}
					</h2>
				</div>
			</div>
		</NcAppContent>
	</NcContent>
</template>

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
import AppSettingsDialog from './components/AppSettingsDialog'
import AlphaTab from './components/AlphaTab'
import FileUpload from './components/FileUpload'
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
    AppSettingsDialog,
		AlphaTab,
    FileUpload,
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
			updating: false,
			loading: true,
      filename: null,
      alphaTex: null,
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
		/**
		 * Returns true if a song is selected and its title is not empty
		 *
		 * @return {boolean}
		 */
		savePossible() {
			return this.currentSong && this.currentSong.title !== ''
		},
	},
	/**
	 * Fetch list of songs when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/guitarsongbook/songs'))
			this.songs = response.data
		} catch (e) {
			console.error(e)
			showError(t('guitarsongbook', 'Could not fetch songs'))
		}
    //this.filename = 'canon.gp'
    this.alphaTex = "\\title 'Test' . 3.3.4"
		this.loading = false
	},

	methods: {
    fileUploaded(filename) {
      this.filename = filename
    },
    scoreLoaded(score) {
      console.log('scoreLoaded', score)
      this.score = score
    },

    /**
		 * Create a new song and focus the song content field automatically
		 *
		 * @param {object} song Song object
		 */
		openSong(song) {
			if (this.updating) {
				return
			}
			this.currentSongId = song.id
			this.$nextTick(() => {
				this.$refs.name.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new song or save
		 */
		saveSong() {
			if (this.currentSongId === -1) {
				this.createSong(this.currentSong)
			} else {
				this.updateSong(this.currentSong)
			}
		},
		/**
		 * Create a new song and focus the song content field automatically
		 * The song is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newSong() {
			if (this.currentSongId !== -1) {
				this.currentSongId = -1
				this.songs.push({
					id: -1,
					name: '',
					title: '',
				})
				this.$nextTick(() => {
					this.$refs.name.focus()
				})
			}
		},
		/**
		 * Abort creating a new song
		 */
		cancelNewSong() {
			this.songs.splice(this.songs.findIndex((song) => song.id === -1), 1)
			this.currentSongId = null
		},
		/**
		 * Create a new song by sending the information to the server
		 *
		 * @param {object} song Song object
		 */
		async createSong(song) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/guitarsongbook/songs'), song)
				const index = this.songs.findIndex((match) => match.id === this.currentSongId)
				this.$set(this.songs, index, response.data)
				this.currentSongId = response.data.id
			}
      catch (e) {
				console.error(e)
				showError(t('guitarsongbook', 'Could not create the song'))
			}
			this.updating = false
		},
		/**
		 * Update an existing song on the server
		 *
		 * @param {object} song Song object
		 */
		async updateSong(song) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/guitarsongbook/songs/${song.id}`), song)
			}
      catch (e) {
				console.error(e)
				showError(t('guitarsongbook', 'Could not update the song'))
			}
			this.updating = false
		},
		/**
		 * Delete a song, remove it from the frontend and show a hint
		 *
		 * @param {object} song Song object
		 */
		async deleteSong(song) {
			try {
				await axios.delete(generateUrl(`/apps/guitarsongbook/songs/${song.id}`))
				this.songs.splice(this.songs.indexOf(song), 1)
				if (this.currentSongId === song.id) {
					this.currentSongId = null
				}
				showSuccess(t('guitarsongbook', 'Song deleted'))
			}
      catch (e) {
				console.error(e)
				showError(t('guitarsongbook', 'Could not delete the song'))
			}
		},
    // ---------------------------
    // Settings
    // ---------------------------
    openSettingsDialog() {
      // this.firstName = ''
      this.lastName = ''
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

div.main-wrapper {
  width: 100%;
  padding: 1rem;
}

div.main-wrapper input[type='text'] {
  width: 100%;
}

div.main-wrapper textarea {
  flex-grow: 1;
  width: 100%;
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
