<template>
  <div class="file-upload">
    <input
        :id="buttonId"
        ref="input"
        type="file"
        :accept="accept"
        class="hidden-visually"
        :disabled="loading || disabled"
        @change="change($event)"/>
    <NcAppNavigationNew
        :text="text"
        :disabled="disabled"
        @click="$refs.input.click()">
      <template #icon>
        <LoadingIcon v-if="loading" :size="20" class="animation-rotate" />
        <UploadIcon v-if="!loading" :size="20" />
      </template>
    </NcAppNavigationNew>
  </div>
</template>

<!--suppress ExceptionCaughtLocallyJS, JSCheckFunctionSignatures, JSUnusedLocalSymbols -->
<script>
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew'
import LoadingIcon from 'vue-material-design-icons/Loading'
import UploadIcon from 'vue-material-design-icons/Upload'

export default {
  name: 'FileSelect',
  components: {
    NcAppNavigationNew,
    LoadingIcon,
    UploadIcon,
  },
  props: {
    buttonId: {
      type: String,
      default: '',
    },
    text: {
      type: String,
      required: true,
    },
    accept: {
      type: String,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    }
  },
  emits: {
    change(file) {
      return true
    }
  },
  methods: {
    change(event) {
      this.$emit('change', event.target.files[0]);
    }
  },
}
</script>

<!--suppress CssUnresolvedCustomProperty -->
<style>
div.file-upload input[type="file"] {
  display: none;
}
</style>
