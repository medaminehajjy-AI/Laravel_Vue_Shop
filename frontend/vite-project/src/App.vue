<template>
  <div class="app-container">
    <Navbar />
    <main class="main-content">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';

export default {
  components: {
    Navbar,
    Footer
  }
}
</script>

<style>
body {
    margin: 0px;
  }

.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-content {
  flex: 1;
  padding: 0px 4px;
  max-width: 1400px;
  width: 100%;
  margin: 0 auto;
  box-sizing: border-box;
}

@media (max-width: 768px) {
  .main-content {
    padding: 20px 16px;
  }
}
</style>