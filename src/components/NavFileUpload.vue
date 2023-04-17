<!--suppress CssUnresolvedCustomProperty -->
<template>
  <NcAppNavigationNew
      :text="t('guitarsongbook', 'Upload Guitar Pro File')"
      :disabled="uploading"
      button-class="icon-upload"
      @click="uploadFile($event)">
    <template #icon>
      <UploadIcon :size="20" />
    </template>
  </NcAppNavigationNew>
</template>

<!--suppress ExceptionCaughtLocallyJS, JSUnresolvedFunction_, JSCheckFunctionSignatures_ -->
<script>
import NcAppNavigationNew from '@nextcloud/vue/dist/Components/NcAppNavigationNew'
import UploadIcon from 'vue-material-design-icons/Upload'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
  name: 'NavFileUpload',
  components: {
    NcAppNavigationNew,
    UploadIcon,
  },
  data() {
    return {
      uploading: false,
    }
  },
  methods: {
    // async uploadFile(event) {
    //   this.uploading = true
    //   try {
    //     const formData = new FormData();
    //     formData.append('file', event.target.files[0]);
    //     const response = await axios.post(
    //         generateUrl('/apps/guitarsongbook/upload'),
    //         formData,
    //         {headers: {'Content-Type': 'multipart/form-data'}}
    //     )
    //     const filename = response.data;
    //     alert(filename)
    //     //api.load('load/' + encodeURIComponent(song));
    //   }
    //   catch (e) {
    //     console.log(e.response ? e.response.data : e.message)
    //     showError(t('guitarsongbook', 'Could not upload the file.'))
    //   }
    //   this.uploading = false
    // },

    // Upload mit File System Access API
    // File System Access API: https://developer.mozilla.org/en-US/docs/Web/API/File_System_Access_API
    // How to Use Fetch: https://dmitripavlutin.com/javascript-fetch-async-await/
    // MIME types: https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Common_types
    // alphaTab file importers: https://www.alphatab.net/docs/introduction#the-file-importers
    async uploadFile()
    {
      this.uploading = true
      try {
        // select a local file
        const [fileHandle] = await window.showOpenFilePicker({
          types: [{
            description: 'GuitarPro, alphaTex, CapXML, MusicXML',
            accept: {
              'application/octet-stream': ['.gp3', '.gp4', '.gp5', '.gp'], // Guitar Pro 3-5 files which are a proprietary binary format from Arobas Music
              'application/zip': ['.gp'], // Guitar Pro 7 files which are a zip archive storing the music information as XML
              'application/xml': ['.gpx', '.cap', '.xml'], // Guitar Pro 6 files (.gpx), CapXML files (.cap) and MusicXML files (.xml)
              'text/plain': ['.txt'], // alphaTex
            }
          }],
          excludeAcceptAllOption: true,
          multiple: false,
        });
        const file = await fileHandle.getFile();

        // upload file
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

<style>
</style>
