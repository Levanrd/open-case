<script setup>
import AccountantLayout from '@/Layouts/AccountantLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Download, FileQuestion, MessageSquarePlus, Save } from '@lucide/vue'

defineOptions({ layout: AccountantLayout })

const props = defineProps({
  caseRecord: { type: Object, required: true },
})

const statusForm = useForm({
  status: props.caseRecord.status === 'accountant_assigned' ? 'under_review' : props.caseRecord.status,
  final_recommendation: props.caseRecord.final_recommendation || '',
})

const noteForm = useForm({
  note: '',
  visibility: 'client',
})

const documentForm = useForm({
  document_type: '',
  request_note: '',
})

function updateStatus() {
  statusForm.patch(`/accountant/cases/${props.caseRecord.id}/status`)
}

function addNote() {
  noteForm.post(`/accountant/cases/${props.caseRecord.id}/notes`, {
    onSuccess: () => noteForm.reset('note'),
  })
}

function requestDocument() {
  documentForm.post(`/accountant/cases/${props.caseRecord.id}/document-requests`, {
    onSuccess: () => documentForm.reset(),
  })
}

function updateChecklist(item, status) {
  useForm({ status, description: item.description }).patch(`/accountant/checklist/${item.id}`)
}
</script>

<template>
  <Head :title="`Review ${caseRecord.case_number}`" />

  <div class="space-y-5">
    <section class="surface p-4 sm:p-5">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <h1 class="text-2xl font-semibold">{{ caseRecord.case_number }}</h1>
          <p class="mt-1 text-sm text-muted">{{ caseRecord.user?.name }} · {{ caseRecord.business?.registered_name }}</p>
        </div>
        <Link href="/accountant/cases" class="btn-secondary">Back to assigned cases</Link>
      </div>
    </section>

    <section class="grid gap-5 xl:grid-cols-[1fr_360px]">
      <div class="space-y-5">
        <div class="surface p-4">
          <h2 class="section-title">Assessment</h2>
          <p class="mt-2 text-sm leading-6 text-muted">{{ caseRecord.result?.generated_summary }}</p>
          <div class="mt-4 grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Risk</p>
              <p class="mt-1 text-2xl font-semibold">{{ caseRecord.risk_level }}</p>
            </div>
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Score</p>
              <p class="mt-1 text-2xl font-semibold">{{ caseRecord.result?.risk_score }}</p>
            </div>
            <div class="rounded-lg border border-line p-3">
              <p class="text-xs uppercase text-muted">Payment</p>
              <p class="mt-1 text-sm font-semibold capitalize">{{ String(caseRecord.payments?.[0]?.status || 'pending').replaceAll('_', ' ') }}</p>
            </div>
          </div>
        </div>

        <div class="surface p-4">
          <h2 class="section-title">Checklist</h2>
          <div class="mt-3 space-y-3">
            <div v-for="item in caseRecord.checklist_items" :key="item.id" class="rounded-lg border border-line p-3">
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                  <p class="font-medium">{{ item.title }}</p>
                  <p v-if="item.description" class="mt-1 text-sm text-muted">{{ item.description }}</p>
                </div>
                <select class="input sm:w-44" :value="item.status" @change="updateChecklist(item, $event.target.value)">
                  <option value="pending">Pending</option>
                  <option value="in_progress">In progress</option>
                  <option value="done">Done</option>
                  <option value="blocked">Blocked</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="surface overflow-hidden">
          <div class="border-b border-line p-4">
            <h2 class="section-title">Documents</h2>
          </div>
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>File</th>
                  <th>Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="document in caseRecord.documents" :key="document.id">
                  <td>{{ document.document_type }}</td>
                  <td>{{ document.original_filename || document.request_note || 'Requested' }}</td>
                  <td><span class="badge-neutral">{{ document.status }}</span></td>
                  <td class="text-right">
                    <a v-if="document.file_path" :href="`/documents/${document.id}/download`" class="btn-secondary">
                      <Download class="h-4 w-4" />
                      Download
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <aside class="space-y-5">
        <div class="surface p-4">
          <h2 class="section-title">Case status</h2>
          <form class="mt-4 space-y-3" @submit.prevent="updateStatus">
            <select v-model="statusForm.status" class="input">
              <option value="under_review">Under review</option>
              <option value="needs_more_documents">Needs more documents</option>
              <option value="ready_for_bir_processing">Ready for BIR processing</option>
              <option value="completed">Completed</option>
            </select>
            <textarea v-model="statusForm.final_recommendation" class="input min-h-28" placeholder="Final recommendation"></textarea>
            <button class="btn-primary w-full" type="submit" :disabled="statusForm.processing">
              <Save class="h-4 w-4" />
              Save status
            </button>
          </form>
        </div>

        <div class="surface p-4">
          <h2 class="section-title">Add note</h2>
          <form class="mt-4 space-y-3" @submit.prevent="addNote">
            <select v-model="noteForm.visibility" class="input">
              <option value="client">Client visible</option>
              <option value="internal">Internal only</option>
            </select>
            <textarea v-model="noteForm.note" class="input min-h-28" required></textarea>
            <button class="btn-primary w-full" type="submit" :disabled="noteForm.processing">
              <MessageSquarePlus class="h-4 w-4" />
              Add note
            </button>
          </form>
          <div class="mt-4 space-y-2">
            <div v-for="note in caseRecord.notes" :key="note.id" class="rounded-lg bg-slate-50 p-3">
              <p class="text-sm">{{ note.note }}</p>
              <p class="mt-2 text-xs text-muted">{{ note.visibility }} · {{ note.user?.name }}</p>
            </div>
          </div>
        </div>

        <div class="surface p-4">
          <h2 class="section-title">Request document</h2>
          <form class="mt-4 space-y-3" @submit.prevent="requestDocument">
            <input v-model="documentForm.document_type" class="input" placeholder="Document type" required />
            <textarea v-model="documentForm.request_note" class="input min-h-24" placeholder="Request note"></textarea>
            <button class="btn-primary w-full" type="submit" :disabled="documentForm.processing">
              <FileQuestion class="h-4 w-4" />
              Send request
            </button>
          </form>
        </div>
      </aside>
    </section>
  </div>
</template>
