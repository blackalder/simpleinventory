<template>
<modal name="demo-login" transition="pop-out" :width="modalWidth" :focus-trap="true" :height="400">
  <div class="box">
    <div class="partition" id="partition-register">
        <div class="partition-title">CREATE CUSTOMER</div>
        <div class="partition-form">
          <form autocomplete="false">
            <input id="n-email" type="text" placeholder="Nama">
            <input id="n-email" type="text" placeholder="Contact">
            <input id="n-email" type="text" placeholder="Alamat">
            <input id="n-email" type="text" placeholder="Diskon">
            <input id="n-email" type="text" placeholder="Tipe Diskon">
            
            <select id="n-tipe" name="adas">
              <option>fix</option>
              <option>percent</option>
            </select>
            
          </form>

          <button class="large-btn">Submit</button>
          
        </div>
      </div>
  </div>
</modal>
</template>
<script>
const MODAL_WIDTH = 656
export default {
  name: 'DemoLoginModal',
  data() {
    return {
      modalWidth: MODAL_WIDTH
    }
  },
  mounted(){
    this.$modal.show('demo-login');
  },
  created() {
    this.modalWidth =
      window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
  },
  methods: {
    signIn() {
      alert('Sign in')
    },
    register() {
      alert('Register')
    }
  }
}
</script>
<style lang="scss">

</style>