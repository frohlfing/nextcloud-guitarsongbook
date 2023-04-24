<template>
</template>

<script>
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon'

export default {
  name: 'Dummy',
  components: {
    NcLoadingIcon,
  },
  props: {
    buttonId: {
      type: String, // String, Number, Boolean, Array, Object, Date, Function, Symbol
      required: false,  // [Boolean, Number]
      default: '',
      validator(value) {
        // The value must match one of these strings
        return ['success', 'warning', 'danger'].includes(value)
      }
    }
  },
  emits: {
    change(filename) {
      if (!filename) {
        console.warn('Invalid event parameter!')
        return false
      }
      return true
    }
  },
  data() {
    return {
      filename: this.buttonId,
    }
  },
  computed: {
    emptyFilename() {
      return this.filename === ''
    }
  },
  mounted() {
    this.filename = ''
  },
  methods: {
    setFilename(filename) {
      this.filename = filename
      this.$emit('change', filename);
    }
  },
  watch: {
    filename(newFilename, oldFilename) {
      console.log('filename has changed')
    }
  }
}
</script>

<style>
</style>
