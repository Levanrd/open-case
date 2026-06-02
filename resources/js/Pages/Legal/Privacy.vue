<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: GuestLayout })
</script>

<template>
  <Head title="Privacy" />
  <section class="surface mx-auto max-w-3xl p-6">
    <h1 class="text-2xl font-semibold">Privacy policy</h1>
    <div class="mt-4 space-y-4 text-sm leading-7 text-muted">
      <p>Open Case stores user-provided business details and uploaded documents only for case review and service coordination.</p>
      <p>TIN values are encrypted at rest, document files are stored privately, and accountant access is limited to assigned cases.</p>
      <p>Open Case does not ask for BIR portal login credentials and is not an official BIR service.</p>
    </div>
  </section>
</template>
