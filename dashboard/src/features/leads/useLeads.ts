import { useQuery } from '@tanstack/react-query'
import { fetchLeads } from './leadsApi'

export function leadsQueryKey(organizationId: string) {
  return ['leads', organizationId] as const
}

export function useLeads(organizationId: string) {
  return useQuery({
    queryKey: leadsQueryKey(organizationId),
    queryFn: () => fetchLeads(organizationId),
  })
}
