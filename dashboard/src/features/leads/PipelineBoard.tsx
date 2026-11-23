import {
  DndContext,
  DragOverlay,
  type Announcements,
  type DragEndEvent,
  type DragStartEvent,
  type UniqueIdentifier,
} from '@dnd-kit/core'
import { useState } from 'react'
import { useCurrentTime } from '../../lib/useCurrentTime'
import type { Lead } from './leadsApi'
import { LeadCardDetails } from './LeadCardDetails'
import { getStageLabel, isLeadStage, leadStages, type LeadStage } from './leadStages'
import { PipelineColumn } from './PipelineColumn'
import { useBoardSensors } from './useBoardSensors'

type PipelineBoardProps = {
  leads: Lead[]
  onMoveLead: (lead: Lead, stage: LeadStage) => void
}

const screenReaderInstructions = {
  draggable:
    'Press Enter to open a lead. To move it, press Space to pick it up, use the left and right arrow keys to choose a column, then press Space to drop it. Press Escape to cancel.',
}

function describeColumn(columnId: UniqueIdentifier | undefined): string {
  return isLeadStage(columnId) ? getStageLabel(columnId) : 'no column'
}

export function PipelineBoard({ leads, onMoveLead }: PipelineBoardProps) {
  const currentTime = useCurrentTime()
  const [draggedLeadId, setDraggedLeadId] = useState<UniqueIdentifier | null>(null)

  const sensors = useBoardSensors()

  const findLead = (leadId: UniqueIdentifier) => leads.find((lead) => lead.id === leadId)
  const leadName = (leadId: UniqueIdentifier) => findLead(leadId)?.full_name ?? 'Lead'
  const draggedLead = draggedLeadId === null ? undefined : findLead(draggedLeadId)

  const announcements: Announcements = {
    onDragStart: ({ active }) => {
      const lead = findLead(active.id)
      return `Picked up ${leadName(active.id)} from ${lead === undefined ? 'its column' : getStageLabel(lead.stage)}.`
    },
    onDragOver: ({ active, over }) => `${leadName(active.id)} is over ${describeColumn(over?.id)}.`,
    onDragEnd: ({ active, over }) =>
      over === null
        ? `${leadName(active.id)} was dropped outside the board and did not move.`
        : `Moved ${leadName(active.id)} to ${describeColumn(over.id)}.`,
    onDragCancel: ({ active }) => `Cancelled. ${leadName(active.id)} did not move.`,
  }

  function handleDragStart(event: DragStartEvent) {
    setDraggedLeadId(event.active.id)
  }

  function handleDragEnd({ active, over }: DragEndEvent) {
    setDraggedLeadId(null)
    const lead = findLead(active.id)
    if (lead !== undefined && over !== null && isLeadStage(over.id) && over.id !== lead.stage) {
      onMoveLead(lead, over.id)
    }
  }

  return (
    <DndContext
      sensors={sensors}
      accessibility={{ announcements, screenReaderInstructions }}
      onDragStart={handleDragStart}
      onDragEnd={handleDragEnd}
      onDragCancel={() => setDraggedLeadId(null)}
      // Gentler edge scrolling than the default, so on a phone a held card does not race past the target column.
      autoScroll={{ threshold: { x: 0.1, y: 0.15 }, acceleration: 4 }}
    >
      {/* On phones one column fills most of the screen; swiping snaps to the next column. */}
      <div className="flex snap-x snap-proximity gap-4 overflow-x-auto pb-4 lg:snap-none">
        {leadStages.map((stage) => (
          <PipelineColumn
            key={stage}
            stage={stage}
            leads={leads.filter((lead) => lead.stage === stage)}
            currentTime={currentTime}
          />
        ))}
      </div>

      <DragOverlay>
        {draggedLead !== undefined && (
          <div className="cursor-grabbing rounded-lg border border-navy bg-white p-4 shadow-lg">
            <LeadCardDetails lead={draggedLead} currentTime={currentTime} />
          </div>
        )}
      </DragOverlay>
    </DndContext>
  )
}
