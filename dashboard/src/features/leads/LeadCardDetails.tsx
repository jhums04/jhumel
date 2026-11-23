import { Badge } from '../../components/Badge'
import { formatCurrency, formatTimeSince } from '../../lib/format'
import type { Lead } from './leadsApi'

const scoreBadges = {
  high: { variant: 'solid', label: 'High' },
  medium: { variant: 'outline', label: 'Medium' },
  low: { variant: 'muted', label: 'Low' },
} as const

type LeadCardDetailsProps = {
  lead: Lead
  currentTime: Date
}

// What a card shows. Used by the card in its column and by the copy that follows the pointer while dragging.
export function LeadCardDetails({ lead, currentTime }: LeadCardDetailsProps) {
  const scoreBadge = lead.score === null ? null : scoreBadges[lead.score]

  return (
    <>
      <p className="font-semibold">{lead.full_name}</p>
      {lead.company !== null && <p className="text-sm text-gray-600">{lead.company}</p>}
      <div className="mt-3 flex items-center justify-between gap-2">
        {scoreBadge === null ? (
          <Badge variant="dashed">Unscored</Badge>
        ) : (
          <Badge variant={scoreBadge.variant}>{scoreBadge.label}</Badge>
        )}
        <span className="text-sm font-semibold">
          {lead.estimated_price === null ? 'No estimate' : formatCurrency(lead.estimated_price)}
        </span>
      </div>
      <p className="mt-2 text-xs text-gray-600">
        <time dateTime={lead.created_at}>{formatTimeSince(new Date(lead.created_at), currentTime)}</time>
      </p>
    </>
  )
}
