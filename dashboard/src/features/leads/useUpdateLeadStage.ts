import { useMutation, useQueryClient } from '@tanstack/react-query'
import { updateLeadStage, type Lead } from './leadsApi'
import type { LeadStage } from './leadStages'
import { moveLeadToStage } from './moveLeadToStage'
import { leadActivitiesQueryKey, leadQueryKey } from './useLead'
import { leadsQueryKey } from './useLeads'

export type LeadMove = {
  lead: Lead
  stage: LeadStage
}

// Optimistic update: the card moves at once, and moves back if Supabase rejects the change.
export function useUpdateLeadStage(organizationId: string) {
  const queryClient = useQueryClient()
  const queryKey = leadsQueryKey(organizationId)

  return useMutation({
    mutationFn: ({ lead, stage }: LeadMove) => updateLeadStage(lead.id, stage),
    onMutate: async ({ lead, stage }) => {
      await queryClient.cancelQueries({ queryKey })
      const previousLeads = queryClient.getQueryData<Lead[]>(queryKey)
      if (previousLeads !== undefined) {
        queryClient.setQueryData<Lead[]>(queryKey, moveLeadToStage(previousLeads, lead.id, stage))
      }
      return { previousLeads }
    },
    onError: (_error, _move, onMutateResult) => {
      if (onMutateResult?.previousLeads !== undefined) {
        queryClient.setQueryData<Lead[]>(queryKey, onMutateResult.previousLeads)
      }
    },
    onSettled: async (_result, _error, { lead }) => {
      await Promise.all([
        queryClient.invalidateQueries({ queryKey }),
        queryClient.invalidateQueries({ queryKey: leadQueryKey(lead.id) }),
        queryClient.invalidateQueries({ queryKey: leadActivitiesQueryKey(lead.id) }),
      ])
    },
  })
}
