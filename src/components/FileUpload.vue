<template>
  <div>
  <input
      :id="buttonId"
      ref="input"
      type="file"
      :accept="accept"
      class="hidden-visually"
      :disabled="uploading || disabled"
      @change="uploadFile($event)"/>
  <NcAppNavigationNew
      :text="text"
      :disabled="disabled"
      @click="$refs.input.click()">
    <template #icon>
      <LoadingIcon v-if="uploading" :size="20" class="animation-rotate" />
      <UploadIcon v-if="!uploading" :size="20" />
    </template>
  </NcAppNavigationNew>
  </div>
</template>

<!--suppress ExceptionCaughtLocallyJS, JSCheckFunctionSignatures -->
<script>
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew'
import LoadingIcon from 'vue-material-design-icons/Loading'
import UploadIcon from 'vue-material-design-icons/Upload'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
  name: 'FileUpload',
  components: {
    NcAppNavigationNew,
    LoadingIcon,
    UploadIcon,
  },
  props: {
    buttonId: {
      type: String,
      required: false,
      default: '',
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
    text: {
      type: String,
      required: true,
    },
    accept: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      uploading: false,
    }
  },
  methods: {
    async uploadFile(event)
    {
      this.uploading = true
      try {
        const file = event.target.files[0];
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || null;
        const formData = new FormData();
        formData.append('file', file);
        const response = await fetch(generateUrl('/apps/guitarsongbook/upload'), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrf,
          },
          body: formData,
        });
        if (!response.ok) {  // status ist nicht 200-299?
          const isJson = response.headers.get('content-type')?.includes('application/json');
          const message = isJson ? await response.json() : `An error has occured: ${response.status}`;
          throw new Error(message);
        }
        const filename = await response.json();
        this.$emit('uploaded', filename);
      }
      catch (e) {
        console.log(e.response ? e.response.data : e.message)
        showError(t('guitarsongbook', 'Could not upload the file.'))
      }
      this.uploading = false
    }
  },
}
</script>

<!--suppress CssUnresolvedCustomProperty -->
<style>
input[type="file"] {
  display: none;
}
</style>
