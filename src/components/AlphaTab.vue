<template>
  <div>
    <LoadingIndicator v-if="this.rendering"/>
    <div id="alphaTab"
         ref="alphaTab"
         data-useworkers="false">
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
    }
  },
  emits: {
    scoreLoaded(score) {
      return true
    }
  },
  mounted() {
    console.log('AppTab: MOUNTED')
    this.api = new alphaTab.AlphaTabApi(this.$refs.alphaTab, {})
    this.api.renderStarted.on(() => {
      this.rendering = true
    })
    this.api.renderFinished.on(() => {
      this.rendering = false
    })
    this.api.scoreLoaded.on((score) => {
      this.$emit('scoreLoaded', score);
    })
    this.api.load(this.gpFile)
  },
  watch: {
    gpFile(value) {
      console.log('AppTab: WATCH gpFile', value)
      this.api.load(value)
    }
  }
}
</script>
