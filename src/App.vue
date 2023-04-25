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
            @click="showModal">
          <template #icon>
            <CogIcon :size="20" />
          </template>
        </NcAppNavigationNew>
        <NcAppSettingsDialog
            :open.sync="modal"
            @close="closeModal"
            :title="t('guitarsongbook', 'Songbook settings')"
            :show-navigation="true">
          <NcAppSettingsSection
              id="settings-songs-folder"
              :title="t('guitarsongbook', 'Songs folder')"
              class="app-settings-section">
            <fieldset>
              <ul>
                <li>
                  <NcButton class="rescan" @click="rescan">
                    <template #icon>
                      <LoadingIcon v-if="scanningLibrary" />
                      <ReloadIcon v-else />
                    </template>
                    {{ t("guitarsongbook", "Rescan library") }}
                  </NcButton>
                </li>
                <li>
                  <label class="settings-input">
                    {{ t("guitarsongbook", "Songs folder") }}
                  </label>
                  <input
                      type="text"
                      :value="songsFolder"
                      :placeholder="t('guitarsongbook', 'Please pick a folder')"
                      @click="pickSongsFolder"/>
                </li>
                <li>
                  <label class="settings-input">{{ t("guitarsongbook", "Update interval in minutes") }}</label>
                  <input
                      v-model="updateInterval"
                      type="number"
                      class="input settings-input"
                      placeholder="0"/>
                </li>
              </ul>
            </fieldset>
          </NcAppSettingsSection>
          <NcAppSettingsSection
              id="settings-info-blocks"
              :title="t('cookbook', 'Info blocks')"
              class="app-settings-section">
            <fieldset>
              <legend class="settings-info">
                {{ t("guitarsongbook", "Control which blocks of information are shown in the recipe view. If you do not use some features and find them distracting, you may hide them.") }}
              </legend>
              <ul>
                <li>
                  <input
                      id="info-blocks-checkbox-preparation-time"
                      v-model="visiblePreparationTime"
                      type="checkbox"
                      class="checkbox"
                      value="1"
                  />
                  <label for="info-blocks-checkbox-preparation-time">
                    {{ t("guitarsongbook", "Preparation time") }}
                  </label>
                </li>
              </ul>
            </fieldset>
          </NcAppSettingsSection>
          <div class="buttom-form">
            <h2>Please enter your name</h2>
            <NcTextField
                label="First Name"
                :value.sync="firstName" />
            <NcTextField
                label="Last Name"
                :value.sync="lastName" />
            <NcButton
                :disabled="!firstName || !lastName"
                @click="closeModal"
                type="primary">
              Submit
            </NcButton>
          </div>
        </NcAppSettingsDialog>
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
            @click=""
        >
          <template #icon>
            <PencilIcon :size="20" />
          </template>
          {{ t("guitarsongbook", "Edit") }}
        </NcButton>
        <NcButton
            v-if="true"
            type="primary"
            :aria-label="t('guitarsongbook', 'Save')"
            @click=""
        >
          <template #icon>
            <LoadingIcon
                v-if="false"
                :size="20"
                class="animation-rotate"
            />
            <CheckmarkIcon v-else :size="20" />
          </template>
          {{ t('guitarsongbook', 'Save') }}
        </NcButton>
        <NcActions
            v-if="true"
            :force-menu="true"
            class="overflow-menu"
        >
          <NcActionButton
              v-if="true"
              :icon="false ? 'icon-loading-small' : 'icon-history'"
              class="action-button"
              :aria-label="t('guitarsongbook', 'Reload')"
              @click=""
          >
            {{ t("guitarsongbook", "Reload") }}
          </NcActionButton>
          <NcActionButton
              v-if="true"
              class="action-button"
              :aria-label="t('guitarsongbook', 'Print')"
              @click=""
          >
            <template #icon="">
              <printer-icon :size="20" />
            </template>
            {{ t('guitarsongbook', 'Print') }}
          </NcActionButton>
          <NcActionButton
              v-if="true"
              icon="icon-delete"
              class="action-button"
              :aria-label="t('guitarsongbook', 'Delete')"
              @click=""
          >
            {{ t('guitarsongbook', 'Delete') }}
          </NcActionButton>
          <NcActionButton
              v-if="true"
              class="action-button"
              :aria-label="t('guitarsongbook', 'Cancel')"
              @click=""
          >
            {{ t('guitarsongbook', 'Cancel') }}
            <template #icon>
              <NcLoadingIcon
                  v-if="false"
                  :size="20"
              />
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
import NcModal from '@nextcloud/vue/dist/Components/NcModal'
import NcAppSettingsDialog from '@nextcloud/vue/dist/Components/NcAppSettingsDialog'
import NcAppSettingsSection from '@nextcloud/vue/dist/Components/NcAppSettingsSection'
import NcActions from '@nextcloud/vue/dist/Components/NcActions'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton'
import NcActionInput from '@nextcloud/vue/dist/Components/NcActionInput'
import NcButton from '@nextcloud/vue/dist/Components/NcButton'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField'
import AlphaTab from './components/AlphaTab'
import FileUpload from './components/FileUpload'
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon"
import PlusIcon from 'vue-material-design-icons/Plus'
import ReloadIcon from 'vue-material-design-icons/Reload'
import CogIcon from 'vue-material-design-icons/Cog'
import PencilIcon from "vue-material-design-icons/Pencil.vue"
import LoadingIcon from "vue-material-design-icons/Loading.vue"
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
    NcModal,
    NcAppSettingsDialog,
    NcAppSettingsSection,
    NcActions,
		NcActionButton,
		NcActionInput,
		NcButton,
    NcTextField,
		AlphaTab,
    FileUpload,
    NcLoadingIcon,
    PlusIcon,
    ReloadIcon,
    CogIcon,
    PrinterIcon,
    PencilIcon,
    LoadingIcon,
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
      modal: false,
      firstName: '',
      lastName: '',
      scanningLibrary: false,
      songsFolder: "",
      updateInterval: 0,
      visiblePreparationTime: true,
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
    // Settings
    showModal() {
      // this.firstName = ''
      this.lastName = ''
      this.modal = true
      //emit(SHOW_SETTINGS_EVENT)
    },
    closeModal() {
      this.modal = false
    },
    pickSongsFolder() {

    },
    rescan() {

    },
    fileUploaded(filename) {
      //alert(filename)
      this.filename = filename
    },

    scoreLoaded(score) {
      console.log('scoreLoaded', score)
      const trackList = document.querySelector(".at-track-list");
      // trackItem.onclick = (e) => {
      //   e.stopPropagation();
      //   api.renderTracks([track]);
      // };
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
	},
}
</script>

<style scoped>

/*Settings*/

div.app-settings legend.settings-info {
  margin-bottom: 10px;
}

div.app-settings button.rescan {
  margin-bottom: 10px;
}

/*div.modal-settings-content {*/
/*  margin: 50px;*/
/*  text-align: center;*/
/*}*/

div.app-settings div.buttom-form .input-field {
  margin: 12px 0;
}

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

<style>
/*Settings*/
/*#app-settings input[type="text"],*/
/*#app-settings input[type="number"],*/
/*#app-settings .button.disable {*/
/*  display: block;*/
/*  width: 100%;*/
/*}*/
</style>
