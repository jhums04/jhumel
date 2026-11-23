import { Button } from '../../components/Button'
import type { Lead } from '../leads/leadsApi'
import { getStageLabel, type LeadStage } from '../leads/leadStages'
import { PipelineBoard } from '../leads/PipelineBoard'
import { useLeads } from '../leads/useLeads'
import { useUpdateLeadStage } from '../leads/useUpdateLeadStage'
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
  const leads = useLeads(organization.id)
  const updateLeadStage = useUpdateLeadStage(organization.id)

  function moveLead(lead: Lead, stage: LeadStage) {
    updateLeadStage.mutate({ lead, stage })
  }

  return (
    <>
      <h1 className="text-2xl font-bold">Pipeline</h1>

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
      {leads.isSuccess && (
        <div className="mt-6">
          <PipelineBoard leads={leads.data} onMoveLead={moveLead} />
        </div>
      )}
    </>
  )
}

export function PipelinePage() {
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
