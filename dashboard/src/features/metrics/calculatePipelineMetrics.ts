import type { Lead } from '../leads/leadsApi'

export type FirstReply = {
  leadId: string
  repliedAt: string
}

export type PipelineMetrics = {
  leadsToday: number
  totalLeads: number
  openPipelineValue: number
  openLeads: number
  wonLeads: number
  lostLeads: number
  // Null when no lead is won or lost yet, because the rate would divide by zero.
  conversionRate: number | null
  // Null when no lead on the board has a reply yet.
  averageResponseMilliseconds: number | null
  repliedLeads: number
}

// The definitions are in docs/ARCHITECTURE.md under "Metrics definitions".
// startOfToday is local midnight, passed in so the result does not depend on the machine's timezone.
export function calculatePipelineMetrics(
  leads: Lead[],
  firstReplies: FirstReply[],
  startOfToday: Date,
): PipelineMetrics {
  const leadsToday = leads.filter((lead) => new Date(lead.created_at) >= startOfToday).length

  const openLeads = leads.filter((lead) => lead.stage !== 'won' && lead.stage !== 'lost')
  const openPipelineValue = openLeads.reduce((total, lead) => total + (lead.estimated_price ?? 0), 0)

  const wonLeads = leads.filter((lead) => lead.stage === 'won').length
  const lostLeads = leads.filter((lead) => lead.stage === 'lost').length
  const closedLeads = wonLeads + lostLeads

  const responseMilliseconds = leads.flatMap((lead) => {
    const firstReply = firstReplies.find((reply) => reply.leadId === lead.id)
    return firstReply === undefined
      ? []
      : [new Date(firstReply.repliedAt).getTime() - new Date(lead.created_at).getTime()]
  })
  const totalResponseMilliseconds = responseMilliseconds.reduce((total, milliseconds) => total + milliseconds, 0)

  return {
    leadsToday,
    totalLeads: leads.length,
    openPipelineValue,
    openLeads: openLeads.length,
    wonLeads,
    lostLeads,
    conversionRate: closedLeads === 0 ? null : wonLeads / closedLeads,
    averageResponseMilliseconds:
      responseMilliseconds.length === 0 ? null : totalResponseMilliseconds / responseMilliseconds.length,
    repliedLeads: responseMilliseconds.length,
  }
}
