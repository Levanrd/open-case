<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: GuestLayout })
</script>

<template>
  <Head title="Terms" />
  <section class="surface mx-auto max-w-3xl p-6">
    <h1 class="text-2xl font-semibold">Terms of service</h1>
    <div class="mt-4 space-y-4 text-sm leading-7 text-muted">
      <p>Open Case provides preliminary readiness review based on user-provided information and documents.</p>
      <p>Results are not final tax advice, do not guarantee closure, and do not compute exact penalties. Final advice should come from a qualified accountant, tax practitioner, lawyer, or BIR officer.</p>
      <p>Open Case service fees are separate from BIR taxes, penalties, government fees, and filing costs.</p>
    </div>
  </section>
</template>
