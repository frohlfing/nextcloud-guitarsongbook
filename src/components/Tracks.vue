<template>
  <div>
    <h3>Tracks</h3>
    <ul class="tracks">
      <li v-for="track in tracks" :key="track.index" :class="{ active: track.index === index }" @click="click(track.index)">
        {{ track.name || ('Track ' + (track.index + 1)) }}
      </li>
    </ul>
  </div>
</template>

<script>
//import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon'

export default {
  name: 'Tracks',
  components: {
    //NcLoadingIcon,
  },
  props: {
    tracks: {
      type: Array,
      require: true
    },
    index: {
      type: Number,
      default: 0
    }
  },
  methods: {
    click(index) {
      this.$emit('update:index', index);
    }
  }
}
</script>

<style scoped>
  ul.tracks li {
    cursor: pointer;
  }
  ul.tracks li.active {
    color: red;
  }
</style>
