import { useDroppable } from '@dnd-kit/core'
import type { Lead } from './leadsApi'
import { LeadCard } from './LeadCard'
import { getStageLabel, type LeadStage } from './leadStages'

type PipelineColumnProps = {
  stage: LeadStage
  leads: Lead[]
  currentTime: Date
}

export function PipelineColumn({ stage, leads, currentTime }: PipelineColumnProps) {
  const { setNodeRef, isOver } = useDroppable({ id: stage })
  const headingId = `pipeline-column-${stage}`

  return (
    <section
      ref={setNodeRef}
      aria-labelledby={headingId}
      className={`flex w-[85vw] max-w-72 shrink-0 snap-start flex-col rounded border p-3 lg:w-auto lg:max-w-none lg:min-w-0 lg:flex-1 ${isOver ? 'border-blue bg-gray-50' : 'border-gray-200 bg-gray-50'}`}
    >
      <h2 id={headingId} className="flex items-baseline justify-between px-1 text-sm font-semibold">
        {getStageLabel(stage)}
        <span className="font-normal text-gray-600">{leads.length}</span>
      </h2>

      {leads.length === 0 ? (
        <p className="mt-3 px-1 text-sm text-gray-600">No leads</p>
      ) : (
        <ul className="mt-3 flex flex-col gap-2">
          {leads.map((lead) => (
            <LeadCard key={lead.id} lead={lead} currentTime={currentTime} />
          ))}
        </ul>
      )}
    </section>
  )
}
