import { useQuery } from '@tanstack/react-query'
import { fetchLead, fetchLeadActivities } from './leadsApi'

export function leadQueryKey(leadId: string) {
  return ['lead', leadId] as const
}

export function leadActivitiesQueryKey(leadId: string) {
  return ['lead-activities', leadId] as const
}

export function useLead(leadId: string) {
  return useQuery({ queryKey: leadQueryKey(leadId), queryFn: () => fetchLead(leadId) })
}

export function useLeadActivities(leadId: string) {
  return useQuery({ queryKey: leadActivitiesQueryKey(leadId), queryFn: () => fetchLeadActivities(leadId) })
}
