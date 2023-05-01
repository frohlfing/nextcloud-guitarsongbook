<template>
  <div class="overlay">
    <div class="loader">
    </div>
  </div>
</template>

<script>

export default {
  name: 'LoadingIndicator',
}
</script>

<style scoped>
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
