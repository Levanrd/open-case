<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })
</script>

<template>
  <Head title="Cases" />
  <section class="surface p-5">
    <h1 class="text-2xl font-semibold">Cases</h1>
    <p class="mt-1 text-sm text-muted">Your case list is available from the owner dashboard.</p>
    <Link href="/owner/dashboard" class="btn-primary mt-4">Open dashboard</Link>
  </section>
</template>
