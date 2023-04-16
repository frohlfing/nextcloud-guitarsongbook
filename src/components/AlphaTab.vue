<template>
  <div>
    <LoadingIndicator v-if="this.rendering"/>
<!--    <div id="alphaTab"-->
<!--         ref="alphaTab"-->
<!--         data-useworkers="false"-->
<!--         data-tex="true">-->
<!--      \title "Hello alphaTab"-->
<!--      .-->
<!--      :4 0.6 1.6 3.6 0.5 2.5 3.5 0.4 2.4 |-->
<!--      3.4 0.3 2.3 0.2 1.2 3.2 0.1 1.1 |-->
<!--      3.1.1-->
<!--    </div>-->
    <div id="alphaTab"
         ref="alphaTab"
         data-useworkers="false">
      Hallo Welt
    </div>
  </div>
</template>

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
  mounted() {
    //const bytes = System.IO.File.OpenRead("http://localhost/laravel9/public/songs/canon.gp")
    const div = this.$refs.alphaTab
    const options = {
      //file: "https://www.alphatab.net/files/canon.gp",
      //file: "http://localhost/laravel9/public/songs/canon.gp",
      //file: "../../data/admin/files/Songs/canon.gp",
    }
    const api = new alphaTab.AlphaTabApi(div, options)
    api.renderStarted.on(() => {
      this.rendering = true
    })
    api.renderFinished.on(() => {
      //const self = this
      //setTimeout(function(){self.rendering = false}, 2000)
      this.rendering = false
    })
    api.load('load/canon.gp');
  },
}

</script>
