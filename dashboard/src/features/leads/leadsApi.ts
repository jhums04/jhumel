import { supabase } from '../../lib/supabase'
import type { Tables } from '../../types/database'
import type { LeadStage } from './leadStages'

export type Lead = Pick<
  Tables<'leads'>,
  'id' | 'full_name' | 'company' | 'score' | 'estimated_price' | 'created_at' | 'stage'
>

export type LeadDetails = Lead &
  Pick<
    Tables<'leads'>,
    'email' | 'phone' | 'services' | 'timeline' | 'message' | 'source' | 'score_reason' | 'draft_subject' | 'draft_body'
  >

export type LeadActivity = Pick<Tables<'lead_activities'>, 'id' | 'type' | 'details' | 'created_at'>

const UUID_PATTERN = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i

export async function fetchLeads(organizationId: string): Promise<Lead[]> {
  const { data, error } = await supabase
    .from('leads')
    .select('id, full_name, company, score, estimated_price, created_at, stage')
    .eq('organization_id', organizationId)
    .order('created_at', { ascending: false })

  if (error) {
    throw error
  }
  return data
}

// Null when the lead does not exist or belongs to an organization the user is not in (row-level security hides it).
export async function fetchLead(leadId: string): Promise<LeadDetails | null> {
  // The id comes from the URL, so anything that is not a UUID cannot be a lead.
  if (!UUID_PATTERN.test(leadId)) {
    return null
  }

  const { data, error } = await supabase
    .from('leads')
    .select(
      'id, full_name, company, score, estimated_price, created_at, stage, email, phone, services, timeline, message, source, score_reason, draft_subject, draft_body',
    )
    .eq('id', leadId)
    .maybeSingle()

  if (error) {
    throw error
  }
  return data
}

export async function fetchLeadActivities(leadId: string): Promise<LeadActivity[]> {
  const { data, error } = await supabase
    .from('lead_activities')
    .select('id, type, details, created_at')
    .eq('lead_id', leadId)
    .order('created_at', { ascending: false })

  if (error) {
    throw error
  }
  return data
}

// Selecting the row back makes a blocked update fail loudly: row-level security returns no row instead of an error.
export async function updateLeadStage(leadId: string, stage: LeadStage): Promise<void> {
  const { error } = await supabase.from('leads').update({ stage }).eq('id', leadId).select('id').single()

  if (error) {
    throw error
  }
}
