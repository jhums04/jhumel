import type { RealtimePostgresChangesPayload } from '@supabase/supabase-js'
import { useQuery, useQueryClient } from '@tanstack/react-query'
import { useEffect, useEffectEvent } from 'react'
import { supabase } from '../../lib/supabase'
import type { Tables } from '../../types/database'
import { applyLeadChange, type LeadChange } from './applyLeadChange'
import { fetchLeads, type Lead } from './leadsApi'
import { leadActivitiesQueryKey, leadQueryKey } from './useLead'

export function leadsQueryKey(organizationId: string) {
  return ['leads', organizationId] as const
}

type LeadRow = Tables<'leads'>

function toLead(row: LeadRow): Lead {
  return {
    id: row.id,
    full_name: row.full_name,
    company: row.company,
    score: row.score,
    estimated_price: row.estimated_price,
    budget: row.budget,
    created_at: row.created_at,
    stage: row.stage,
  }
}

function toLeadChange(payload: RealtimePostgresChangesPayload<LeadRow>): LeadChange | null {
  if (payload.eventType === 'INSERT') {
    return { type: 'inserted', lead: toLead(payload.new) }
  }
  if (payload.eventType === 'UPDATE') {
    return { type: 'updated', lead: toLead(payload.new) }
  }
  return payload.old.id === undefined ? null : { type: 'deleted', leadId: payload.old.id }
}

type UseLeadsOptions = {
  onLeadArrived?: (lead: Lead) => void
}

// Loads the board once, then keeps it current with Supabase Realtime. Row-level security decides which changes arrive.
export function useLeads(organizationId: string, { onLeadArrived }: UseLeadsOptions = {}) {
  const queryClient = useQueryClient()

  const handleChange = useEffectEvent((payload: RealtimePostgresChangesPayload<LeadRow>) => {
    const change = toLeadChange(payload)
    if (change === null) {
      return
    }
    queryClient.setQueryData<Lead[]>(leadsQueryKey(organizationId), (leads) =>
      leads === undefined ? leads : applyLeadChange(leads, change),
    )
    if (change.type === 'inserted') {
      onLeadArrived?.(change.lead)
    } else {
      // An open drawer shows more fields than the board has, so it refetches: a new score, draft, or stage.
      const leadId = change.type === 'updated' ? change.lead.id : change.leadId
      void queryClient.invalidateQueries({ queryKey: leadQueryKey(leadId) })
      void queryClient.invalidateQueries({ queryKey: leadActivitiesQueryKey(leadId) })
    }
  })

  useEffect(() => {
    // A unique name per subscription: React's development double-mount would otherwise reuse a closing channel.
    const channel = supabase
      .channel(`leads:${organizationId}:${crypto.randomUUID()}`)
      .on<LeadRow>(
        'postgres_changes',
        { event: '*', schema: 'public', table: 'leads', filter: `organization_id=eq.${organizationId}` },
        handleChange,
      )
      .subscribe()

    return () => {
      void supabase.removeChannel(channel)
    }
  }, [organizationId])

  return useQuery({
    queryKey: leadsQueryKey(organizationId),
    queryFn: () => fetchLeads(organizationId),
  })
}
