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

<!--suppress ExceptionCaughtLocallyJS, JSCheckFunctionSignatures, JSUnusedLocalSymbols, JSUnusedGlobalSymbols -->
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
    select(file) {
      return true
    }
  },
  methods: {
    change(event) {
      this.$emit('select', event.target.files[0]);
      event.target.value = '' // without the reset, the change event would not fire when selecting the same file again
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
