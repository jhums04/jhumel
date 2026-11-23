import type { LeadDetails } from './leadsApi'
import { getStageLabel, isLeadStage, leadStages } from './leadStages'
import { useUpdateLeadStage } from './useUpdateLeadStage'

// The tap-friendly way to move a lead: dragging across five columns is awkward on a phone, and a select works
// the same for keyboard and screen reader users. It shares the board's optimistic update.
export function LeadStageSelect({ lead }: { lead: LeadDetails }) {
  const updateLeadStage = useUpdateLeadStage(lead.organization_id)
  const selectedStage = updateLeadStage.isPending ? updateLeadStage.variables.stage : lead.stage
  const selectId = `lead-stage-${lead.id}`

  return (
    <div>
      <label htmlFor={selectId} className="block text-sm font-semibold">
        Stage
      </label>
      <select
        id={selectId}
        value={selectedStage}
        disabled={updateLeadStage.isPending}
        onChange={(event) => {
          if (isLeadStage(event.target.value)) {
            updateLeadStage.mutate({ lead, stage: event.target.value })
          }
        }}
        className="mt-2 min-h-11 rounded border border-gray-200 bg-white px-3 text-base"
      >
        {leadStages.map((stage) => (
          <option key={stage} value={stage}>
            {getStageLabel(stage)}
          </option>
        ))}
      </select>
      {updateLeadStage.isError && (
        <p role="alert" className="mt-2 text-sm text-error">
          Could not move this lead to {getStageLabel(updateLeadStage.variables.stage)}. Try again.
        </p>
      )}
    </div>
  )
}
