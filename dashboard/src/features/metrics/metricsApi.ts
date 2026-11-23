import { supabase } from '../../lib/supabase'
import type { FirstReply } from './calculatePipelineMetrics'

// The earliest reply per lead. Rows come oldest first, so the first one seen for each lead is its first reply.
export async function fetchFirstReplies(organizationId: string): Promise<FirstReply[]> {
  const { data, error } = await supabase
    .from('lead_activities')
    .select('lead_id, created_at')
    .eq('organization_id', organizationId)
    .eq('type', 'email_sent')
    .order('created_at', { ascending: true })

  if (error) {
    throw error
  }

  const firstReplies = new Map<string, FirstReply>()
  for (const activity of data) {
    if (!firstReplies.has(activity.lead_id)) {
      firstReplies.set(activity.lead_id, { leadId: activity.lead_id, repliedAt: activity.created_at })
    }
  }
  return [...firstReplies.values()]
}
