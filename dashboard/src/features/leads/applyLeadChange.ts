import type { Lead } from './leadsApi'

export type LeadChange =
  | { type: 'inserted'; lead: Lead }
  | { type: 'updated'; lead: Lead }
  | { type: 'deleted'; leadId: string }

// Applies one realtime change to the cached list. Returns a new list, like every React Query cache update.
export function applyLeadChange(leads: Lead[], change: LeadChange): Lead[] {
  if (change.type === 'inserted') {
    // The same insert can arrive twice: once from realtime and once from a refetch that raced it.
    const isAlreadyOnBoard = leads.some((lead) => lead.id === change.lead.id)
    return isAlreadyOnBoard ? leads : [change.lead, ...leads]
  }
  if (change.type === 'updated') {
    return leads.map((lead) => (lead.id === change.lead.id ? change.lead : lead))
  }
  return leads.filter((lead) => lead.id !== change.leadId)
}
