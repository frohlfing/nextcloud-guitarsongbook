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
          type="primary"
          v-if="!editMode"
          @click="enterEditMode">
        <template #icon>
          <PencilIcon :size="20"/>
        </template>
        {{ t("guitarsongbook", "Edit") }}
      </NcButton>
      <NcButton
          type="primary"
          v-if="editMode"
          @click="updateSong">
        <template #icon>
          <LoadingIcon
              class="animation-rotate"
              v-if="saving"
              :size="20"/>
          <CheckmarkIcon
              v-else
              :size="20" />
        </template>
        {{ t('guitarsongbook', 'Save') }}
      </NcButton>
      <NcActions
          class="overflow-menu"
          :force-menu="true">
        <NcActionButton
            class="action-button"
            v-if="!editMode"
            @click="print">
          <template #icon="">
            <printer-icon :size="20" />
          </template>
          {{ t('guitarsongbook', 'Print') }}
        </NcActionButton>
        <NcActionButton
            class="action-button"
            icon="icon-delete"
            v-if="!editMode"
            @click="deleteSong">
          {{ t('guitarsongbook', 'Delete') }}
        </NcActionButton>
        <NcActionButton
            class="action-button"
            v-if="editMode"
            @click="cancelEditMode">
          {{ t('guitarsongbook', 'Cancel') }}
          <template #icon>
            <eye-icon :size="20" />
          </template>
        </NcActionButton>
      </NcActions>
    </div>
    <AppMain
        :song="currentSong"
        :editable="editMode"
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
      saving: false,
      editMode: false
		}
	},
	methods: {
    // ---------------------------
    // Menü
    // ---------------------------
    enterEditMode() {
      this.editMode = true
    },
    cancelEditMode() {
      console.log('AppContent: cancelEditMode', this.currentSong)
      this.currentSong = this.song;
      this.editMode = false
    },
    async updateSong() {
      console.log('AppContent: updateSong', this.currentSong)
      this.saving = true
      try {
        await api.songs.update(this.currentSong)
        this.$emit('songUpdated', this.currentSong);
      }
      catch (e) {
        console.error(e.response ? e.response.data : e.message)
        showError(t('guitarsongbook', 'Could not save the song'))
      }
      this.saving = false
      this.editMode = false
    },
		async deleteSong() {
      console.log('AppContent: deleteSong', this.currentSong)
      this.saving = true
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
      this.saving = false
		},
    print() {
      console.log('print')
    },
    // ---------------------------
    // AppMain
    // ---------------------------
    songUpdated(song) {
      console.log('AppContent: songUpdated', song)
      this.currentSong = song
      this.$emit('songUpdated', song);
    },
	},
  watch: {
    song(value) {
      console.log('AppContent: WATCH song', value)
      this.currentSong = value
      this.editMode = false
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
