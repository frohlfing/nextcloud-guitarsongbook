<template>
  <div class="form">
    <label for="name">{{ t('guitarsongbook', 'Name') }}</label>
    <input type="text" id="name" ref="name" v-model="newSong.name" :disabled="saving"/>
    <label for="title">{{ t('guitarsongbook', 'Title') }}</label>
    <input type="text" id="title" v-model="newSong.title" :disabled="saving"/>
    <input type="button" class="primary" :value="t('guitarsongbook', 'Ok')" :disabled="!canSubmit" @click="submit"/>
  </div>
</template>

<!--suppress JSUnusedGlobalSymbols, JSUnusedLocalSymbols -->
<script>
import '@nextcloud/dialogs/styles/toast.scss'

export default {
	name: 'AppMain',
	components: {
	},
  props: {
    song: {
      type: Object,
      required: true,
    },
  },
  emits: {
    submit(song, isDirty) {
      return true
    }
  },
	data() {
		return {
      newSong: Object.assign({}, this.song), /** @var Object copy of the song  **/
			saving: false,
		}
	},
  computed: {
    isDirty() {
      let modifed = false
      for (let k in this.newSong) {
        if (this.newSong[k] !== this.song[k]) {
          modifed = true
          break
        }
      }
      return modifed
    },
    canSubmit() {
      return !this.saving && this.newSong.name.length
    }
  },
  created() {
    // wird aufgerufen, wenn mit v-if="true" die Componente eingefügt wird
    console.log('### SongForm created ###');
    window.addEventListener('beforeunload', this.beforeUnload)
  },
  mounted() {
    // wird aufgerufen, nachdem created() ausgeführt wurde
    console.log('SongForm: MOUNTED')
    this.$nextTick(() => {
      this.$refs.name.focus()
    })
  },
  beforeDestroy() {
    // wird aufgerufen, wenn mit v-if="false" die Componente ausgehängt wird
    console.log('### SongForm beforeDestroy ###');
    window.removeEventListener('beforeunload', this.beforeUnload)
  },
  watch: {
    song(value) {
      console.log('SongForm: WATCH song', value)
      this.newSong = Object.assign({}, value)
      this.$nextTick(() => {
        this.$refs.name.focus()
      })
    }
  },
	methods: {
    beforeUnload(e) {
      // Das Event "beforeUnload" wird gefeuert, wenn der User die Webseite verlassen möchte.
      console.log('### SongForm beforeWindowUnload ###', this.isDirty);
      if (this.isDirty) {
        // invoke system dialog: "Webseite verlassen?" Deine Änderungen werden eventuell nicht gespeichert. [Abbrechen] [Verlassen]
        e.preventDefault()  // Cancel the window unload event
        e.returnValue = t('guitarsongbook', 'You have unsaved changes! Do you still want to leave?')
      }
    },
		async submit() {
      console.log('SongForm: BEGIN submit', this.isDirty)
      this.saving = true
      this.$emit('submit', this.newSong, this.isDirty);
      this.saving = false
      console.log('SongForm: END submit')
		}
	}
}
</script>

<style scoped>
div.form input[type='text'] {
  width: 100%;
}
div.form textarea {
  flex-grow: 1;
  width: 100%;
}
</style>
