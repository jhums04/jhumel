import { useDraggable } from '@dnd-kit/core'
import { useLocation, useNavigate } from 'react-router'
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
  const navigate = useNavigate()
  const location = useLocation()
  const isNewArrival = currentTime.getTime() - new Date(lead.created_at).getTime() < NEW_ARRIVAL_MILLISECONDS

  function openLead() {
    void navigate({ pathname: `/leads/${lead.id}`, search: location.search })
  }

  return (
    <li
      ref={setNodeRef}
      {...attributes}
      {...listeners}
      // A drag needs 5px of movement, so a plain click opens the lead. Enter opens it too; Space starts a keyboard drag.
      onClick={openLead}
      onKeyDownCapture={(event) => {
        if (event.key === 'Enter' && !isDragging) {
          openLead()
        }
      }}
      aria-label={`${lead.full_name}${lead.company === null ? '' : `, ${lead.company}`}`}
      className={`cursor-grab rounded border bg-white p-4 ${isNewArrival ? 'border-blue' : 'border-gray-200'} ${isDragging ? 'opacity-40' : ''}`}
    >
      <LeadCardDetails lead={lead} currentTime={currentTime} />
    </li>
  )
}
