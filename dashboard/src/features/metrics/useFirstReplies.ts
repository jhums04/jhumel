import { useQuery } from '@tanstack/react-query'
import { fetchFirstReplies } from './metricsApi'

export function firstRepliesQueryKey(organizationId: string) {
  return ['first-replies', organizationId] as const
}

export function useFirstReplies(organizationId: string) {
  return useQuery({
    queryKey: firstRepliesQueryKey(organizationId),
    queryFn: () => fetchFirstReplies(organizationId),
  })
}
