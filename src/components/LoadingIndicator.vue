<template>
  <div class="at-overlay">
    <div class="at-overlay-content">
      <NcLoadingIcon :size="20"/>
    </div>
  </div>
</template>

<script>
import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon'

export default {
  name: 'LoadingIndicator',
  components: {
    NcLoadingIcon,
  },
}
</script>

<style>
div.at-overlay {
  /** Fill Parent */
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1002;
  /* Blurry dark shade */
  backdrop-filter: blur(3px);
  background: rgba(0, 0, 0, 0.5);
  /** center content */
  display: flex;
  justify-content: center;
  align-items: flex-start;
}
div.at-overlay div.at-overlay-content {
  /* white box with drop-shadow */
  margin-top: 20px;
  background: #fff;
  box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.3);
  padding: 10px;
}
</style>
