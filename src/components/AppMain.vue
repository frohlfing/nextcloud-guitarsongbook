<template>
  <div class="main-wrapper">
    <div v-if="currentSong">
      <AlphaTab :gp-file="gpFile" @score-loaded="scoreLoaded"/>
      <input
        type="text"
        ref="name"
        v-model="currentSong.name"
        :disabled="saving"/>
      <input type="text"
        v-model="currentSong.title"
        :disabled="saving"/>
      <input
        type="button"
        class="primary"
        :value="t('guitarsongbook', 'Save')"
        :disabled="saving || !this.currentSong.name.length"
        @click="updateSong"/>
    </div>
    <div v-else id="emptycontent">
      <div class="icon-file"></div>
      <h2>
        {{ t('guitarsongbook', 'Create a song to get started!') }}
      </h2>
    </div>
  </div>
</template>

<!--suppress JSUnusedGlobalSymbols, JSUnusedLocalSymbols -->
<script>
import AlphaTab from './AlphaTab'
import '@nextcloud/dialogs/styles/toast.scss'
import { showError } from '@nextcloud/dialogs'
import api from '../api'

export default {
	name: 'AppMain',
	components: {
		AlphaTab,
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
    }
  },
	data() {
		return {
      currentSong: this.song,
			saving: false,
		}
	},
	computed: {
    gpFile() {
      return this.currentSong ? api.songs.urlFile(this.currentSong.id) : null
    }
	},
	methods: {
    scoreLoaded(score) {
      console.log('scoreLoaded', score)
    },
    /**
     * Update an existing song on the server
     *
     * __@param {object} song Song object
     */
		async updateSong() {
      console.log('AppMain: SAVE UPDATE SONG', this.currentSong)
			this.saving = true
			try {
        await api.songs.update(this.currentSong)
        console.log('AppMain: UPDATE SONG SAVED', this.currentSong)
        this.$emit('songUpdated', this.currentSong);
			}
      catch (e) {
				console.error(e.response ? e.response.data : e.message)
				showError(t('guitarsongbook', 'Could not save the song'))
			}
			this.saving = false
		}
	},
  watch: {
    song(value) {
      console.log('AppMain: WATCH song', value)
      this.currentSong = value
      // this.$nextTick(() => {
      // 	this.$refs.name.focus()
      // })
    }
  }
}
</script>

<style scoped>
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
</style>
