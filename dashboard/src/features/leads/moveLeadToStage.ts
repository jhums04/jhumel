import type { Lead } from './leadsApi'
import type { LeadStage } from './leadStages'

// Returns a new list, so React Query sees a change and the cached original stays intact for rollback.
export function moveLeadToStage(leads: Lead[], leadId: string, stage: LeadStage): Lead[] {
  return leads.map((lead) => (lead.id === leadId ? { ...lead, stage } : lead))
}
