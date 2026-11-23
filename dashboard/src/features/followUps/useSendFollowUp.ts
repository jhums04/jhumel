import { useMutation, useQueryClient } from '@tanstack/react-query'
import { leadActivitiesQueryKey, leadQueryKey } from '../leads/useLead'
import { sendFollowUp } from './sendFollowUp'

// After sending, n8n has logged the reply and may have moved the lead to Contacted.
export function useSendFollowUp() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: sendFollowUp,
    onSuccess: async (_delivered, { leadId }) => {
      await Promise.all([
        queryClient.invalidateQueries({ queryKey: leadQueryKey(leadId) }),
        queryClient.invalidateQueries({ queryKey: leadActivitiesQueryKey(leadId) }),
        queryClient.invalidateQueries({ queryKey: ['leads'] }),
      ])
    },
  })
}
