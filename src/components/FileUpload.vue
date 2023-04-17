<template>
  <div>
<!--  <NcActionInput-->
<!--      class="upload"-->
<!--      :disabled="uploading ? 'disabled' : null"-->
<!--      :icon="uploading ? 'icon-loading-small' : 'icon-upload'"-->
<!--      :type="file"-->
<!--      @change="uploadFile( $event )">-->
<!--    {{ t('guitarsongbook', 'Upload Guitar Pro File') }}-->
<!--  </NcActionInput>-->

  <input
      type="file"
      @change="uploadFile($event)"
  />
  </div>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
  name: 'FileUpload',
  components: {
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
</style>
