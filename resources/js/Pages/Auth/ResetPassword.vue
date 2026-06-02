<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { KeyRound } from '@lucide/vue'

defineOptions({ layout: GuestLayout })

const props = defineProps({
  token: { type: String, required: true },
  email: { type: String, default: '' },
})

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post('/reset-password')
}
</script>

<template>
  <Head title="Set new password" />
  <section class="surface mx-auto max-w-md p-5">
    <h1 class="text-xl font-semibold">Set new password</h1>
    <form class="mt-5 space-y-4" @submit.prevent="submit">
      <div>
        <label class="label" for="email">Email</label>
        <input id="email" v-model="form.email" class="input mt-1" type="email" required />
        <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
      </div>
      <div>
        <label class="label" for="password">Password</label>
        <input id="password" v-model="form.password" class="input mt-1" type="password" required />
        <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
      </div>
      <div>
        <label class="label" for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" v-model="form.password_confirmation" class="input mt-1" type="password" required />
      </div>
      <button class="btn-primary w-full" type="submit" :disabled="form.processing">
        <KeyRound class="h-4 w-4" />
        Update password
      </button>
    </form>
  </section>
</template>
