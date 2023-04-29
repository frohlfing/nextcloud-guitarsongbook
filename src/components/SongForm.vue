<template>
  <div class="form">
    <label for="name">{{ t('guitarsongbook', 'Name') }}</label>
    <input type="text" id="name" ref="name" v-model="newSong.name" :disabled="saving"/>
    <label for="title">{{ t('guitarsongbook', 'Title') }}</label>
    <input type="text" id="title" v-model="newSong.title" :disabled="saving"/>
    <input type="button" class="primary" :value="t('guitarsongbook', 'Save')" :disabled="saving || !newSong.name.length" @click="submit"/>
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
    submit(song) {
      return true
    }
  },
	data() {
		return {
      newSong: Object.assign({}, this.song), /** @var Object copy of the song  **/
			saving: false,
		}
	},
  mounted() {
    console.log('SongForm: MOUNTED')
    this.$nextTick(() => {
      this.$refs.name.focus()
    })
  },
	methods: {
		async submit() {
      console.log('SongForm: BEGIN submit', this.newSong)
			this.saving = true
      this.$emit('submit', this.newSong);
			this.saving = false
      console.log('SongForm: END submit')
		}
	},
  watch: {
    song(value) {
      console.log('SongForm: WATCH song', value)
      this.newSong = Object.assign({}, value)
      this.$nextTick(() => {
      	this.$refs.name.focus()
      })
    },
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
