import { useDraggable } from '@dnd-kit/core'
import type { Lead } from './leadsApi'
import { LeadCardDetails } from './LeadCardDetails'

// How long a just-arrived lead keeps its highlight. The clock ticks each minute, so the highlight lasts one to two minutes.
const NEW_ARRIVAL_MILLISECONDS = 60_000

type LeadCardProps = {
  lead: Lead
  currentTime: Date
}

export function LeadCard({ lead, currentTime }: LeadCardProps) {
  const { attributes, listeners, setNodeRef, isDragging } = useDraggable({ id: lead.id })
  const isNewArrival = currentTime.getTime() - new Date(lead.created_at).getTime() < NEW_ARRIVAL_MILLISECONDS

  return (
    <li
      ref={setNodeRef}
      {...attributes}
      {...listeners}
      aria-label={`${lead.full_name}${lead.company === null ? '' : `, ${lead.company}`}`}
      className={`cursor-grab rounded border bg-white p-4 ${isNewArrival ? 'border-blue' : 'border-gray-200'} ${isDragging ? 'opacity-40' : ''}`}
    >
      <LeadCardDetails lead={lead} currentTime={currentTime} />
    </li>
  )
}
