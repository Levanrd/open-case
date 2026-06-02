<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Plus } from '@lucide/vue'

defineOptions({ layout: AdminLayout })

defineProps({
  accountants: { type: Array, default: () => [] },
})
</script>

<template>
  <Head title="Accountants" />
  <section class="surface overflow-hidden">
    <div class="flex items-center justify-between border-b border-line p-4">
      <div>
        <h1 class="text-2xl font-semibold">Accountants</h1>
        <p class="mt-1 text-sm text-muted">Internal approved reviewer accounts.</p>
      </div>
      <Link href="/admin/accountants/create" class="btn-primary">
        <Plus class="h-4 w-4" />
        New
      </Link>
    </div>
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Assigned cases</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="accountant in accountants" :key="accountant.id">
            <td class="font-medium">{{ accountant.name }}</td>
            <td>{{ accountant.email }}</td>
            <td>{{ accountant.phone || '—' }}</td>
            <td>{{ accountant.assigned_cases_count || 0 }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>
