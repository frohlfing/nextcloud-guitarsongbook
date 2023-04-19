<template>
  <div class="app-navigation-new">
    <label class="upload-button">
      <span class="upload-icon">
        <LoadingIcon v-if="uploading" :size="20" class="animation-rotate" />
        <slot name="icon">
          <UploadIcon v-if="!uploading" :size="20"/>
        </slot>
      </span>
      <input
          :id="buttonId"
          type="file"
          :accept="accept"
          :disabled="uploading || disabled"
          @change="uploadFile($event)"/>
      <span class="upload-text">
        {{text}}
      </span>
    </label>
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

<style lang="scss" scoped>
.app-navigation-new {
  display: block;
  padding: calc(var(--default-grid-baseline, 4px) * 2);
  button {
    width: 100%;
  }
}
</style>

<!--suppress CssUnresolvedCustomProperty -->
<style>
input[type="file"] {
  display: none;
}
label.upload-button {
  display: flex;
  overflow: hidden;
  position: relative;
  min-height: 44px;
  min-width: 44px;
  border-radius: 22px;
  background-color: var(--color-primary-light);
  font-weight: bold;
  text-overflow: ellipsis;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
label.upload-button:focus:not([disabled]), label.upload-button:hover:not([disabled]) {
  background-color: var(--color-primary-light-hover);
}
span.upload-icon {
  padding: 0 6px 0 6px;
  height: 44px;
  width: 44px;
  min-height: 44px;
  min-width: 44px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}
span.upload-text {
  cursor: pointer;
}
</style>
