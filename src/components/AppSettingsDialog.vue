<template>
  <NcAppSettingsDialog
      :open.sync="isOpen"
      @update:open="close"
      :title="t('guitarsongbook', 'Songbook settings')"
      :show-navigation="true">
    <NcAppSettingsSection
        id="settings-songs-folder"
        class="app-settings-section"
        :title="t('guitarsongbook', 'Songs folder')">
      <fieldset>
        <ul>
          <li>
            <NcButton class="rescan" @click="rescan">
              <template #icon>
                <LoadingIcon v-if="scanningLibrary"/>
                <ReloadIcon v-else/>
              </template>
              {{ t("guitarsongbook", "Rescan library") }}
            </NcButton>
          </li>
          <li>
            <label class="settings-input">
              {{ t("guitarsongbook", "Songs folder") }}
            </label>
            <input
                type="text"
                :value="songsFolder"
                :placeholder="t('guitarsongbook', 'Please pick a folder')"
                @click="pickSongsFolder"/>
          </li>
          <li>
            <label class="settings-input">
              {{ t("guitarsongbook", "Update interval in minutes") }}
            </label>
            <input
                type="number"
                class="input settings-input"
                placeholder="0"
                v-model="updateInterval"/>
          </li>
        </ul>
      </fieldset>
    </NcAppSettingsSection>
    <NcAppSettingsSection
        id="settings-info-blocks"
        class="app-settings-section"
        :title="t('cookbook', 'Info blocks')">
      <fieldset>
        <legend class="settings-info">
          {{ t("guitarsongbook", "Control which blocks of information are shown in the recipe view. If you do not use some features and find them distracting, you may hide them.") }}
        </legend>
        <ul>
          <li>
            <input
                id="info-blocks-checkbox-preparation-time"
                type="checkbox"
                class="checkbox"
                value="1"
                v-model="visiblePreparationTime"
            />
            <label for="info-blocks-checkbox-preparation-time">
              {{ t("guitarsongbook", "Preparation time") }}
            </label>
          </li>
        </ul>
      </fieldset>
    </NcAppSettingsSection>
    <div class="buttom-form">
      <h2>Please enter your name</h2>
      <NcTextField
          label="First Name"
          :value.sync="firstName" />
      <NcTextField
          label="Last Name"
          :value.sync="lastName" />
      <NcButton
          :disabled="!firstName || !lastName"
          @click="close"
          type="primary">
        {{ t("guitarsongbook", "OK") }}
      </NcButton>
    </div>
  </NcAppSettingsDialog>
</template>

<script>
import NcAppSettingsDialog from '@nextcloud/vue/dist/Components/NcAppSettingsDialog'
import NcAppSettingsSection from '@nextcloud/vue/dist/Components/NcAppSettingsSection'
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField'
import NcButton from '@nextcloud/vue/dist/Components/NcButton'
//import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon"
import LoadingIcon from "vue-material-design-icons/Loading.vue"
import ReloadIcon from 'vue-material-design-icons/Reload'
import '@nextcloud/dialogs/styles/toast.scss'
// import { generateUrl } from '@nextcloud/router'
// import { showError, showSuccess } from '@nextcloud/dialogs'
// import axios from '@nextcloud/axios'

export default {
	name: 'AppSettingsDialog',
	components: {
    NcAppSettingsDialog,
    NcAppSettingsSection,
    NcTextField,
    NcButton,
    // NcLoadingIcon,
    LoadingIcon,
    ReloadIcon,
	},
  props: {
    open: {
      type: Boolean,
      default: false,
    }
  },
  emits: {
    close() {
      return true
    }
  },
	data() {
		return {
      isOpen: this.open,
      firstName: '',
      lastName: '',
      scanningLibrary: false,
      songsFolder: "",
      updateInterval: 0,
      visiblePreparationTime: true,
		}
	},
	methods: {
    close() {
      this.isOpen = false
      this.$emit('close');
    },
    pickSongsFolder() {
    },
    rescan() {
    }
	},
  watch: {
    open(value) {
      this.isOpen = value
    }
  }
}
</script>

<!--suppress CssUnusedSymbol -->
<style scoped>
div.app-settings legend.settings-info {
  margin-bottom: 10px;
}
div.app-settings button.rescan {
  margin-bottom: 10px;
}
div.app-settings div.buttom-form .input-field {
  margin: 12px 0;
}
</style>
