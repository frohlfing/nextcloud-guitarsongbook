<template>
  <div>
    <select id="track" :disabled="!tracks" @change="change($event)" title="Tracks">
      <option v-for="track in tracks" :key="track.index" :value="track.index" :selected="track.index === index">
        {{ track.name || ('Track ' + (track.index + 1)) }}
      </option>
    </select>
  </div>
</template>

<script>
import NcSelect from '@nextcloud/vue/dist/Components/NcSelect'

export default {
  name: 'Tracks',
  components: {
    NcSelect,
  },
  props: {
    tracks: {
      type: Array,
      default: []
    },
    index: {
      type: Number,
      default: 0
    }
  },
  methods: {
    change(event) {
      this.$emit('update:index', parseInt(event.target.value, 10));
    }
  }
}
</script>
