import { useState } from 'react'
import { Outlet } from 'react-router'
import { Button } from '../../components/Button'
import { useCurrentTime } from '../../lib/useCurrentTime'
import type { Lead } from '../leads/leadsApi'
import { LeadArrivalAnnouncer } from '../leads/LeadArrivalAnnouncer'
import { getStageLabel, type LeadStage } from '../leads/leadStages'
import { PipelineBoard } from '../leads/PipelineBoard'
import { useLeads } from '../leads/useLeads'
import { useUpdateLeadStage } from '../leads/useUpdateLeadStage'
import { calculatePipelineMetrics } from '../metrics/calculatePipelineMetrics'
import { MetricsBar } from '../metrics/MetricsBar'
import { useFirstReplies } from '../metrics/useFirstReplies'
import type { Organization } from '../organizations/organizationsApi'
import { useCurrentOrganization } from '../organizations/useCurrentOrganization'

function ErrorMessage({ message, onRetry }: { message: string; onRetry: () => void }) {
  return (
    <div
      role="alert"
      className="mt-6 flex flex-wrap items-center justify-between gap-3 rounded border border-error bg-error-background px-4 py-3 text-error"
    >
      <p>{message}</p>
      <Button variant="secondary" onClick={onRetry}>
        Try again
      </Button>
    </div>
  )
}

function OrganizationPipeline({ organization }: { organization: Organization }) {
  const [arrivalAnnouncement, setArrivalAnnouncement] = useState('')
  const leads = useLeads(organization.id, {
    onLeadArrived: (lead) =>
      setArrivalAnnouncement(`New lead: ${lead.full_name}${lead.company === null ? '' : `, ${lead.company}`}`),
  })
  const updateLeadStage = useUpdateLeadStage(organization.id)
  const firstReplies = useFirstReplies(organization.id)
  const currentTime = useCurrentTime()
  const startOfToday = new Date(currentTime.getFullYear(), currentTime.getMonth(), currentTime.getDate())

  function moveLead(lead: Lead, stage: LeadStage) {
    updateLeadStage.mutate({ lead, stage })
  }

  return (
    <>
      <h1 className="text-2xl font-bold">Pipeline</h1>
      <LeadArrivalAnnouncer message={arrivalAnnouncement} />

      {updateLeadStage.isError && (
        <p role="alert" className="mt-4 rounded border border-error bg-error-background px-4 py-3 text-error">
          Could not move {updateLeadStage.variables.lead.full_name} to{' '}
          {getStageLabel(updateLeadStage.variables.stage)}. Try again.
        </p>
      )}

      {leads.isPending && (
        <p role="status" className="mt-6 text-gray-600">
          Loading leads...
        </p>
      )}
      {leads.isError && (
        <ErrorMessage message="Could not load leads." onRetry={() => void leads.refetch()} />
      )}
      {leads.isSuccess && firstReplies.isSuccess && (
        <div className="mt-6">
          <MetricsBar metrics={calculatePipelineMetrics(leads.data, firstReplies.data, startOfToday)} />
        </div>
      )}
      {firstReplies.isError && <p className="mt-6 text-error">Could not load the pipeline summary.</p>}
      {leads.isSuccess && (
        <div className="mt-6">
          <PipelineBoard leads={leads.data} onMoveLead={moveLead} />
        </div>
      )}
    </>
  )
}

// The lead drawer is a child route (/leads/:leadId), drawn over the board.
export function PipelinePage() {
  return (
    <>
      <PipelineContent />
      <Outlet />
    </>
  )
}

function PipelineContent() {
  const { organizations, currentOrganization } = useCurrentOrganization()

  if (organizations.isPending) {
    return (
      <p role="status" className="text-gray-600">
        Loading...
      </p>
    )
  }
  if (organizations.isError) {
    return <ErrorMessage message="Could not load your organizations." onRetry={() => void organizations.refetch()} />
  }
  if (currentOrganization === undefined) {
    return <p className="text-gray-600">Your account is not part of an organization yet.</p>
  }

  // Keyed by organization so switching starts with a clean board and no leftover error message.
  return <OrganizationPipeline key={currentOrganization.id} organization={currentOrganization} />
}
