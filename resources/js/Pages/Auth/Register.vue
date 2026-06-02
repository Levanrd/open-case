<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { UserPlus } from '@lucide/vue'

defineOptions({ layout: GuestLayout })

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  terms: false,
})

function submit() {
  form.post('/register')
}
</script>

<template>
  <Head title="Register" />

  <div class="mx-auto max-w-2xl">
    <section class="surface p-5 sm:p-6">
      <h1 class="text-2xl font-semibold">Create business owner account</h1>
      <p class="mt-2 text-sm leading-6 text-muted">
        Open Case gives preliminary review only. It is not connected to BIR and does not collect BIR taxes, penalties, or government fees.
      </p>

      <form class="mt-6 grid gap-4 sm:grid-cols-2" @submit.prevent="submit">
        <div>
          <label class="label" for="first_name">First name</label>
          <input id="first_name" v-model="form.first_name" class="input mt-1" required />
          <p v-if="form.errors.first_name" class="error">{{ form.errors.first_name }}</p>
        </div>
        <div>
          <label class="label" for="last_name">Last name</label>
          <input id="last_name" v-model="form.last_name" class="input mt-1" required />
          <p v-if="form.errors.last_name" class="error">{{ form.errors.last_name }}</p>
        </div>
        <div class="sm:col-span-2">
          <label class="label" for="email">Email</label>
          <input id="email" v-model="form.email" class="input mt-1" type="email" autocomplete="email" required />
          <p v-if="form.errors.email" class="error">{{ form.errors.email }}</p>
        </div>
        <div class="sm:col-span-2">
          <label class="label" for="phone">Phone</label>
          <input id="phone" v-model="form.phone" class="input mt-1" autocomplete="tel" />
          <p v-if="form.errors.phone" class="error">{{ form.errors.phone }}</p>
        </div>
        <div>
          <label class="label" for="password">Password</label>
          <input id="password" v-model="form.password" class="input mt-1" type="password" autocomplete="new-password" required />
          <p v-if="form.errors.password" class="error">{{ form.errors.password }}</p>
        </div>
        <div>
          <label class="label" for="password_confirmation">Confirm password</label>
          <input id="password_confirmation" v-model="form.password_confirmation" class="input mt-1" type="password" autocomplete="new-password" required />
        </div>
        <label class="flex gap-3 text-sm leading-6 text-muted sm:col-span-2">
          <input v-model="form.terms" type="checkbox" class="mt-1 h-4 w-4 rounded border-line text-primary" />
          <span>I accept the preliminary-review disclaimer, privacy policy, and terms of service.</span>
        </label>
        <p v-if="form.errors.terms" class="error sm:col-span-2">{{ form.errors.terms }}</p>

        <div class="flex flex-col gap-3 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
          <button class="btn-primary" type="submit" :disabled="form.processing">
            <UserPlus class="h-4 w-4" />
            Create account
          </button>
          <Link href="/login" class="text-sm font-medium text-primary">I already have an account</Link>
        </div>
      </form>
    </section>
  </div>
</template>
