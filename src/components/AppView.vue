<template>
  <div class="main-wrapper">
    <div v-if="currentSong">
      <AlphaTab :gp-file="gpFile" @score-loaded="scoreLoaded"/>
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
import api from '../api'

export default {
	name: 'AppView',
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
	},
  watch: {
    song(value) {
      console.log('AppMain: WATCH song', value)
      this.currentSong = value
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
