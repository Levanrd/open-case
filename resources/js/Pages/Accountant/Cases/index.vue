<script setup>
import AccountantLayout from '@/Layouts/AccountantLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ArrowRight, ClipboardCheck } from '@lucide/vue'

defineOptions({ layout: AccountantLayout })

defineProps({
  cases: { type: Array, default: () => [] },
})

function badgeClass(status) {
  if (status === 'completed') return 'badge-success'
  if (status === 'needs_more_documents') return 'badge-warning'
  if (['under_review', 'accountant_assigned', 'ready_for_bir_processing'].includes(status)) return 'badge-info'
  return 'badge-neutral'
}
</script>

<template>
  <Head title="Assigned Cases" />
  <section class="surface overflow-hidden">
    <div class="border-b border-line p-4">
      <h1 class="flex items-center gap-2 text-2xl font-semibold">
        <ClipboardCheck class="h-6 w-6 text-primary" />
        Assigned cases
      </h1>
      <p class="mt-1 text-sm text-muted">Only cases assigned to your internal account are visible here.</p>
    </div>
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Case</th>
            <th>Client</th>
            <th>Business</th>
            <th>Risk</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="caseRecord in cases" :key="caseRecord.id">
            <td>
              <p class="font-semibold">{{ caseRecord.case_number }}</p>
              <p class="text-xs capitalize text-muted">{{ String(caseRecord.case_type).replaceAll('_', ' ') }}</p>
            </td>
            <td>{{ caseRecord.user?.name }}</td>
            <td>{{ caseRecord.business?.registered_name }}</td>
            <td>{{ caseRecord.risk_level || 'Pending' }}</td>
            <td><span :class="badgeClass(caseRecord.status)">{{ String(caseRecord.status).replaceAll('_', ' ') }}</span></td>
            <td class="text-right">
              <Link :href="`/accountant/cases/${caseRecord.id}`" class="btn-secondary">
                Review
                <ArrowRight class="h-4 w-4" />
              </Link>
            </td>
          </tr>
          <tr v-if="!cases.length">
            <td colspan="6" class="text-muted">No assigned cases yet.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>
