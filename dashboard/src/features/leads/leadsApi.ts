import { supabase } from '../../lib/supabase'
import type { Tables } from '../../types/database'
import type { LeadStage } from './leadStages'

export type Lead = Pick<
  Tables<'leads'>,
  'id' | 'full_name' | 'company' | 'score' | 'estimated_price' | 'created_at' | 'stage'
>

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

// Selecting the row back makes a blocked update fail loudly: row-level security returns no row instead of an error.
export async function updateLeadStage(leadId: string, stage: LeadStage): Promise<void> {
  const { error } = await supabase.from('leads').update({ stage }).eq('id', leadId).select('id').single()

  if (error) {
    throw error
  }
}
