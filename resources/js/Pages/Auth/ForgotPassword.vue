<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Mail } from '@lucide/vue'

defineOptions({ layout: GuestLayout })

const form = useForm({ email: '' })

function submit() {
  form.post('/forgot-password')
}
</script>

<template>
  <Head title="Reset password" />
  <section class="surface mx-auto max-w-md p-5">
    <h1 class="text-xl font-semibold">Reset password</h1>
    <form class="mt-5 space-y-4" @submit.prevent="submit">
      <div>
        <label class="label" for="email">Email</label>
        <input id="email" v-model="form.email" class="input mt-1" type="email" required />
        <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
      </div>
      <button class="btn-primary w-full" type="submit" :disabled="form.processing">
        <Mail class="h-4 w-4" />
        Send reset link
      </button>
    </form>
    <Link href="/login" class="mt-4 inline-block text-sm font-medium text-primary">Back to login</Link>
  </section>
</template>
