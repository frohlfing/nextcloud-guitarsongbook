<template>
<!--  <div class="at-overlay">-->
<!--    <div class="at-overlay-content">-->
<!--      <NcLoadingIcon :size="20"/>-->
<!--    </div>-->
<!--  </div>-->
  <div class="overlay">
    <div class="loader">
    </div>
  </div>
</template>

<script>
//import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon'

export default {
  name: 'LoadingIndicator',
  components: {
    //NcLoadingIcon,
  },
}
</script>

<style scoped>
/*div.at-overlay {*/
/*  position: absolute;*/
/*  top: 0;*/
/*  left: 0;*/
/*  right: 0;*/
/*  bottom: 0;*/
/*  z-index: 1002;*/
/*  background: rgba(0, 0, 0, 0.05);*/
/*  !** center content *!*/
/*  display: flex;*/
/*  justify-content: center;*/
/*  align-items: flex-start;*/
/*}*/
/*div.at-overlay div.at-overlay-content {*/
/*  margin-top: 20px;*/
/*  background: #fff;*/
/*  box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.3);*/
/*  padding: 10px;*/
/*}*/
div.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: black;
  opacity: 0.05;
}
div.overlay div.loader {
  position: relative;
  left: 50%;
  top: 50%;
  width: 70px;
  height: 70px;
  margin-left: -35px;
  margin-top: -35px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
