import { formatCurrency, formatTimeSince } from '../../lib/format'
import type { Lead } from './leadsApi'
import { ScoreBadge } from './ScoreBadge'
import { getBudgetLabel } from './serviceLabels'

type LeadCardDetailsProps = {
  lead: Lead
  currentTime: Date
}

// What a card shows. Used by the card in its column and by the copy that follows the pointer while dragging.
export function LeadCardDetails({ lead, currentTime }: LeadCardDetailsProps) {
  return (
    <>
      <p className="font-semibold">{lead.full_name}</p>
      {lead.company !== null && <p className="text-sm text-gray-600">{lead.company}</p>}
      <div className="mt-3 flex items-center justify-between gap-2">
        <ScoreBadge score={lead.score} />
        <span className="text-sm font-semibold">
          {lead.estimated_price !== null
            ? formatCurrency(lead.estimated_price)
            : lead.budget !== null
              ? getBudgetLabel(lead.budget)
              : 'No estimate'}
        </span>
      </div>
      <p className="mt-2 text-xs text-gray-600">
        <time dateTime={lead.created_at}>{formatTimeSince(new Date(lead.created_at), currentTime)}</time>
      </p>
    </>
  )
}
