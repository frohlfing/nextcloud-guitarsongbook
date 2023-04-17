<!--suppress CssUnresolvedCustomProperty -->
<template>
  <div>
  <NcActionInput
      :disabled="uploading ? 'disabled' : null"
      :icon="uploading ? 'icon-loading-small' : 'icon-upload'"
      @submit="uploadFile($event)">
    {{ t('guitarsongbook', 'Upload Guitar Pro File') }}
  </NcActionInput>


  <div class="upload">
    <div class="upload-icon">
      <Upload
          :size="20"
          :disabled="uploading ? 'disabled' : null"/>
    </div>
    <label class="upload-input">
      <input type="file" @change="uploadFile($event)"/>
      {{ t('guitarsongbook', 'Upload Guitar Pro File') }}
    </label>
  </div>

  </div>
</template>

<script>

import NcActions from '@nextcloud/vue/dist/Components/NcActions'
import NcActionInput from '@nextcloud/vue/dist/Components/NcActionInput'
import Upload from 'vue-material-design-icons/Upload'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
  name: 'FileUpload',
  components: {
    NcActions,
    NcActionInput,
    Upload,
  },
  data() {
    return {
      uploading: false,
    }
  },
  methods: {
    async uploadFile(event) {
      this.uploading = true
      try {
        const formData = new FormData();
        formData.append('file', event.target.files[0]);
        const response = await axios.post(
            generateUrl('/apps/guitarsongbook/upload'),
            formData,
            {headers: {'Content-Type': 'multipart/form-data'}}
        )
        const filename = response.data;
        alert(filename)
        //api.load('load/' + encodeURIComponent(song));
      }
      catch (e) {
        console.log(e.response ? e.response.data : e.message)
        showError(t('guitarsongbook', 'Could not upload the file.'))
      }
      this.uploading = false
    },
  },
}
</script>

<style>
  div.upload {
    display: flex;
    align-items: center;
    flex: 1 1 auto;
    margin: 4px 0;
    padding-right: 14px;
  }
  input[type="file"] {
    display: none;
  }
  label.upload-input {
    display: flex;
    /*font-size: var(--default-font-size);*/
    /*color: var(--color-main-text);*/
    /*margin: 0;*/
    padding: 4px 12px;
    height: 36px !important;
    width: 100%;
    font-family: var(--font-face);
    color: var(--color-text);
    background-color: var(--color-main-background);
    opacity: .7;
    border: 2px solid var(--color-border-maxcontrast);
    border-radius: var(--border-radius-large);
    text-overflow: ellipsis;
    cursor: pointer;
    -webkit-appearance: textfield !important;
    -moz-appearance: textfield !important;
  }
  label.upload-input:focus:not([disabled]), label.upload-input:hover:not([disabled]) {
    opacity: 1;
    border-color: var(--color-primary-element);
  }
  .upload-icon {
    padding: 0 6px 0 6px;
  }
</style>
