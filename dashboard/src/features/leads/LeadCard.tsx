import { useDraggable } from '@dnd-kit/core'
import type { Lead } from './leadsApi'
import { LeadCardDetails } from './LeadCardDetails'

type LeadCardProps = {
  lead: Lead
  currentTime: Date
}

export function LeadCard({ lead, currentTime }: LeadCardProps) {
  const { attributes, listeners, setNodeRef, isDragging } = useDraggable({ id: lead.id })

  return (
    <li
      ref={setNodeRef}
      {...attributes}
      {...listeners}
      aria-label={`${lead.full_name}${lead.company === null ? '' : `, ${lead.company}`}`}
      className={`cursor-grab rounded border border-gray-200 bg-white p-4 ${isDragging ? 'opacity-40' : ''}`}
    >
      <LeadCardDetails lead={lead} currentTime={currentTime} />
    </li>
  )
}
