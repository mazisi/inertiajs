
<template>
  <div class="page-header align-items-start min-vh-100" style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80');
    ">
    <span class="mask bg-gradient-dark opacity-6"></span>
    <div class="container my-auto">
      <div class="row">
        <div class="col-lg-4 col-md-8 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div
                class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1"
              >
                <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                  Sign in
                </h4>
                <div class="row mt-3">
                  <div class="col-2 text-center ms-auto">
                    <a class="btn btn-link px-3" href="javascript:;">
                      <i class="fab fa-facebook text-white text-lg"></i>
                    </a>
                  </div>
                  <div class="col-2 text-center px-1">
                    <a class="btn btn-link px-3" href="javascript:;">
                      <i class="fab fa-github text-white text-lg"></i>
                    </a>
                  </div>
                  <div class="col-2 text-center me-auto">
                    <a class="btn btn-link px-3" href="javascript:;">
                      <i class="fab fa-google text-white text-lg"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body">
  <form @submit.prevent="submit" role="form" class="text-start mt-3">
  <div class="mb-3">
    <div class="input-group input-group-outline null is-filled">
    <label class="form-label">Email</label>
    <input v-model="form.email" type="email" autocomplete="on" class="form-control form-control-default">
    </div>
    <p v-if="errors.email" class="text-danger">{{ errors.email }}</p>
  </div>
  <div class="mb-3">
    <div class="input-group input-group-outline null is-filled">
    <label class="form-label">Password</label>
    <input v-model="form.password" type="password" class="form-control form-control-default" >
    </div>
    <p v-if="errors.password" class="text-danger">{{ errors.password }}</p>
  </div>
  <div class="form-check form-switch d-flex">
  <input v-model="form.remember"  class="form-check-input" type="checkbox">
  <label class="form-check-label ms-3" for="rememberMe">Remember me</label>
   </div>
  <div class="text-center">
  
  <button :disabled="form.processing" type="submit" class="btn mb-0 bg-gradient-success btn-md w-100 null my-4 mb-2 d-flex justify-content-center"> 

  <span class="mr-5" v-if="form.processing">
  <div class="half-circle-spinner">
  <div class="circle circle-1"></div>
  <div class="circle circle-2"></div>
</div>
</span>
 Sign In
  </button>
  </div>
</form>

            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer position-absolute bottom-2 py-2 w-100">
      <div class="container">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-12 col-md-6 my-auto">
            <div class="copyright text-center float-right text-sm text-white text-lg-start">
              © {{ new Date().getFullYear() }}, Copyright &copy;
              <a
                href="#"
                class="font-weight-bold text-white"
                target="_blank">Slotow Family Trust</a
              >
            </div>
          </div>
         
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3';
import { HalfCircleSpinner } from 'epic-spinners'

export default {
  name: "sign-in",
 props: {
    errors: Object,
  },

  setup () {

    const form = useForm({
        email: '',
        password: '',
        remember: '',   
    })

    function submit() {
      form.post('/login', {
        onFinish: () => form.reset('password'),
      })
      
    }

    return { form, submit }
  },

   components: {
    HalfCircleSpinner
  },
  
};

</script>
