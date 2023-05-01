<template>
  <div>
    <LoadingIndicator v-if="rendering"/>
    <div id="alphaTab" ref="alphaTab" data-useworkers="false">
    </div>
  </div>
</template>

<!--suppress JSUnusedLocalSymbols -->
<script>
import LoadingIndicator from './LoadingIndicator'

export default {
  name: 'AlphaTab',
  components: {
    LoadingIndicator,
  },
  data() {
    return {
      rendering: false,
    }
  },
  props: {
    gpFile: {
      type: String,
      required: true,
    },
    trackIndex: {
      type: Number,
      default: 0
    }
  },
  emits: {
    scoreLoaded(score) {
      return true
    }
  },
  watch: {
    gpFile(value) {
      console.log('AlphaTab: WATCH gpFile', value)
      this.api.load(value)
      this.rendering = true
    },
    trackIndex(value) {
      console.log('AlphaTab: WATCH trackIndex', value)
      if (this.api.score && this.api.tracks[0].index !== value) {
        const track = this.api.score.tracks[value]
        console.log('AlphaTab: Render track', track)
        this.api.renderTracks([track]);
      }
    }
  },
  mounted() {
    console.log('AlphaTab: MOUNTED')
    this.api = new alphaTab.AlphaTabApi(this.$refs.alphaTab, {})
    this.api.renderStarted.on((resize) => {
      this.rendering = true
      console.log('AlphaTab: renderStarted')
    })
    this.api.renderFinished.on(() => {
      this.rendering = false
      console.log('AlphaTab: renderFinished')
    })
    this.api.scoreLoaded.on((score) => {
      this.$emit('score-loaded', score);
      // if (this.api.tracks[0].index !== this.trackIndex) {
      //   const track = this.api.score.tracks[this.trackIndex]
      //   console.log('AlphaTab: Render track (2)', track)
      //   this.api.renderTracks([track]);
      // }
    })
    console.log('AlphaTab: Load Score')
    this.api.load(this.gpFile, [this.trackIndex])
    this.rendering = true
  }
}
</script>
