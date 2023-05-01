<!--suppress CssUnresolvedCustomProperty -->
<template>
  <NcAppContent>
    <div class="app-controls">
      <div class="location">
        <h2>
          {{ currentSong?.name  || '' }}
        </h2>
      </div>
<!--      <NcButton v-if="!editMode" type="primary" @click="enterEditMode">-->
<!--        <template #icon>-->
<!--          <PencilIcon :size="20"/>-->
<!--        </template>-->
<!--        {{ t('guitarsongbook', 'Edit') }}-->
<!--      </NcButton>-->
<!--      <NcButton v-if="editMode" type="primary" @click="updateSong">-->
<!--        <template #icon>-->
<!--          <LoadingIcon v-if="saving" class="animation-rotate" :size="20"/>-->
<!--          <CheckmarkIcon v-else :size="20"/>-->
<!--        </template>-->
<!--        {{ t('guitarsongbook', 'Save') }}-->
<!--      </NcButton>-->
      <NcActions v-if="currentSong" :open.sync="actionMenuIsOpen" class="overflow-menu" :force-menu="true">
        <NcActionButton v-if="!editMode" class="action-button" @click="print">
          <template #icon="">
            <printer-icon :size="20"/>
          </template>
          {{ t('guitarsongbook', 'Print') }}
        </NcActionButton>
        <NcActionButton v-if="!editMode" class="action-button" @click="enterEditMode">
          <template #icon="">
            <PencilIcon :size="20"/>
          </template>
          {{ t('guitarsongbook', 'Edit') }}
        </NcActionButton>
        <NcActionButton v-if="!editMode" class="action-button" icon="icon-delete" @click="deleteSong">
          {{ t('guitarsongbook', 'Delete') }}
        </NcActionButton>
        <NcActionButton v-if="editMode" class="action-button" @click="cancelEditMode">
          {{ t('guitarsongbook', 'Cancel') }}
          <template #icon>
            <eye-icon :size="20"/>
          </template>
        </NcActionButton>
      </NcActions>
    </div>
    <div class="app-main">
      <div v-if="currentSong">
        <div v-if="!editMode">
          <Tracks :tracks="tracks" :index.sync="trackIndex"></Tracks>
          <AlphaTab :gp-file="gpFile" :track-index="trackIndex" @score-loaded="scoreLoaded"/>
        </div>
        <SongForm v-if="editMode" :song="currentSong" @submit="updateSong"/>
      </div>
      <div v-if="!currentSong" id="emptycontent">
        <div class="icon-file"></div>
        <h2>
          {{ t('guitarsongbook', 'Welcome') }}
        </h2>
      </div>
    </div>
  </NcAppContent>
</template>

<!--suppress ExceptionCaughtLocallyJS, JSUnusedLocalSymbols -->
<script>
import NcActions from '@nextcloud/vue/dist/Components/NcActions'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton'
import NcButton from '@nextcloud/vue/dist/Components/NcButton'
import AlphaTab from './AlphaTab'
import Tracks from './Tracks'
import SongForm from './SongForm'
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
    AlphaTab,
    Tracks,
    SongForm,
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
    },
  },
  emits: {
    updated(song) {
      return true
    },
    deleted(song) {
      return true
    },
    editable(b) {
      return true
    },
  },
  data() {
    return {
      currentSong: this.song,
      // todo options hinzufügen, dass die Daten aus options.json hat (oder besser als computed, wenn computed cached)
      score: null,
      trackIndex: 0, // todo trackIndex mit options.trackIndex initialisieren
      saving: false,
      editMode: false,
      actionMenuIsOpen: false,
    }
  },
  computed: {
    gpFile() {
      return this.currentSong ? api.songs.urlFile(this.currentSong.id) : null
    },
    tracks() {
      return this.score?.tracks || []
    }
  },
  watch: {
    song(value) {
      console.log('AppContent: WATCH song', value)
      this.currentSong = value
      // todo options aus options.json laden falls nicht als computed realisiert
      this.trackIndex = 0  // todo trackIndex mit options.trackIndex initialisieren
      this.editMode = false
    },
    editMode(value) {
      console.log('AppContent: WATCH editMode', value)
      this.$emit('editable', value)
    }
  },
  methods: {
    // ---------------------------
    // Menü
    // ---------------------------
    enterEditMode() {
      this.actionMenuIsOpen = false
      this.editMode = true
      //this.$emit('editable', true)
    },
    cancelEditMode() {
      this.actionMenuIsOpen = false
      this.editMode = false
      //this.$emit('editable', false)
    },
    async deleteSong() {
      this.actionMenuIsOpen = false
      console.log('AppContent: deleteSong', this.currentSong)
      this.saving = true
      try {
        await api.songs.destroy(this.currentSong)
        this.$emit('deleted', this.currentSong)
        this.currentSong = null
        // todo options = null wenn nicht als computed realisiert
        this.trackIndex = 0
        showSuccess(t('guitarsongbook', 'Song deleted'))
      }
      catch (e) {
        console.log(e.response ? e.response.data : e.message)
        showError(t('guitarsongbook', 'Could not delete the song'))
      }
      this.saving = false
    },
    print() {
      this.actionMenuIsOpen = false
      console.log('print')
    },
    // ---------------------------
    // AlphaTab
    // ---------------------------
    scoreLoaded(score) {
      console.log('AlphaContent: scoreLoaded', score)
      this.score = score
    },
    // ---------------------------
    // SongForm
    // ---------------------------
    async updateSong(song, isDirty) {
      if (isDirty) {
        console.log('AppContent: updateSong', isDirty)
        this.saving = true
        try {
          await api.songs.update(song)
          this.currentSong = song
          this.$emit('updated', song)
        } catch (e) {
          console.error(e.response ? e.response.data : e.message)
          showError(t('guitarsongbook', 'Could not save the song'))
        }
        this.saving = false
      }
      this.editMode = false
    },
    // todo async updateOptions(options)
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
div.app-main {
  width: 100%;
  padding: 1rem;
}
</style>
