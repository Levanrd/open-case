<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineOptions({ layout: AdminLayout })

defineProps({
  cases: { type: Object, required: true },
})

function statusLabel(status) {
  return String(status || '').replaceAll('_', ' ')
}
</script>

<template>
  <Head title="Admin Cases" />
  <section class="surface overflow-hidden">
    <div class="border-b border-line p-4">
      <h1 class="text-2xl font-semibold">Cases</h1>
      <p class="mt-1 text-sm text-muted">All Open Case submissions.</p>
    </div>
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Case</th>
            <th>Owner</th>
            <th>Business</th>
            <th>Risk</th>
            <th>Accountant</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="caseRecord in cases.data" :key="caseRecord.id">
            <td><Link :href="`/admin/cases/${caseRecord.id}`" class="font-semibold text-primary">{{ caseRecord.case_number }}</Link></td>
            <td>{{ caseRecord.user?.name }}</td>
            <td>{{ caseRecord.business?.registered_name }}</td>
            <td>{{ caseRecord.risk_level || 'Pending' }}</td>
            <td>{{ caseRecord.assigned_accountant?.name || 'Unassigned' }}</td>
            <td><span class="badge-neutral capitalize">{{ statusLabel(caseRecord.status) }}</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>
