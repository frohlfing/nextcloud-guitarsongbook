<!--suppress CssUnresolvedCustomProperty -->
<template>
  <NcAppContent>
    <div class="app-controls">
      <div class="location">
        <h2>
          {{ t('guitarsongbook', 'New song') }}
        </h2>
      </div>
      <NcButton
          v-if="true"
          type="primary"
          :aria-label="t('guitarsongbook', 'Edit')"
          @click="enterEditMode">
        <template #icon>
          <PencilIcon :size="20" />
        </template>
        {{ t("guitarsongbook", "Edit") }}
      </NcButton>
      <NcButton
          type="primary"
          v-if="true"
          :aria-label="t('guitarsongbook', 'Save')"
          @click="saveSong">
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
            @click="reload">
          {{ t("guitarsongbook", "Reload") }}
        </NcActionButton>
        <NcActionButton
            class="action-button"
            v-if="true"
            :aria-label="t('guitarsongbook', 'Print')"
            @click="print">
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
            @click="deleteSong">
          {{ t('guitarsongbook', 'Delete') }}
        </NcActionButton>
        <NcActionButton
            class="action-button"
            v-if="true"
            :aria-label="t('guitarsongbook', 'Cancel')"
            @click="cancelEditMode">
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
</template>

<!--suppress ExceptionCaughtLocallyJS, JSUnusedLocalSymbols -->
<script>
import NcActions from '@nextcloud/vue/dist/Components/NcActions'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton'
import NcButton from '@nextcloud/vue/dist/Components/NcButton'
import AppMain from './AppMain'
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon'
import LoadingIcon from 'vue-material-design-icons/Loading.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import PrinterIcon from 'vue-material-design-icons/Printer.vue'
import EyeIcon from 'vue-material-design-icons/Eye.vue'
import CheckmarkIcon from 'vue-material-design-icons/Check.vue'
import '@nextcloud/dialogs/styles/toast.scss'
import { showError, showSuccess } from '@nextcloud/dialogs'
import api from '../api'

export default {
	name: 'App',
	components: {
		NcAppContent,
    NcActions,
		NcActionButton,
		NcButton,
    AppMain,
    NcLoadingIcon,
    LoadingIcon,
    PencilIcon,
    PrinterIcon,
    EyeIcon,
    CheckmarkIcon,
	},
  props: {
    song: {
      type: Object,
      default: null,
    }
  },
  emits: {
    songUpdated(song) {
      return true
    },
    songDeleted(song) {
      return true
    }
  },
	data() {
		return {
      currentSong: this.song,
			loading: false,
      updating: false,
		}
	},
	methods: {
    // ---------------------------
    // Menü
    // ---------------------------
    enterEditMode() {
      console.log('enterEditMode')
    },
    cancelEditMode() {
      console.log('cancelEditMode')
    },
    saveSong() {
      console.log('saveSong')
    },
		/**
		 * Delete a song, remove it from the frontend and show a hint
		 *
		 * __@param {object} song Song object
		 */
		async deleteSong() {
      this.updating = true
			try {
				await api.songs.delete(this.currentSong)
        this.$emit('songDeleted', this.currentSong);
        this.currentSong = null
				showSuccess(t('guitarsongbook', 'Song deleted'))
			}
      catch (e) {
        console.log(e.response ? e.response.data : e.message)
				showError(t('guitarsongbook', 'Could not delete the song'))
			}
      this.updating = false
		},
    reload() {
      console.log('reload')
    },
    print() {
      console.log('print')
    },
    // ---------------------------
    // AppMain
    // ---------------------------
    songUpdated(song) {
      this.currentSong = song
      this.$emit('songUpdated', song);
    },
	},
  watch: {
    song(value) {
      console.log('AppContent: WATCH song', value)
      this.currentSong = value
    }
  }
}
</script>

<style scoped>
div.app-controls {
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
div.app-controls div.location {
   display: flex;
   flex: 1;
   flex-direction: column;
   justify-content: center;
 }
div.app-controls div.location h2 {
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
