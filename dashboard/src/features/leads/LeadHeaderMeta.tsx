import { formatCurrency, formatTimeSince } from '../../lib/format'
import { useCurrentTime } from '../../lib/useCurrentTime'
import type { LeadDetails } from './leadsApi'
import { getBudgetLabel } from './serviceLabels'

// On the navy header the board's navy badge would vanish, so the score gets its own treatment here:
// High stands out in amber, Medium and Low stay quieter.
const scoreStyles = {
  high: { label: 'High', className: 'bg-amber text-black' },
  medium: { label: 'Medium', className: 'bg-white/15 text-white' },
  low: { label: 'Low', className: 'bg-white/10 text-navy-muted' },
} as const

export function LeadHeaderMeta({ lead }: { lead: LeadDetails }) {
  const currentTime = useCurrentTime()
  const score = lead.score === null ? null : scoreStyles[lead.score]
  const value =
    lead.estimated_price !== null
      ? `${formatCurrency(lead.estimated_price)} estimate`
      : lead.budget !== null
        ? `${getBudgetLabel(lead.budget)} budget`
        : null

  return (
    <div className="flex flex-wrap items-center gap-2 text-sm">
      {score === null ? (
        <span className="rounded-full border border-dashed border-navy-muted px-3 py-1 text-navy-muted">Unscored</span>
      ) : (
        <span className={`rounded-full px-3 py-1 font-semibold ${score.className}`}>{score.label} score</span>
      )}
      {value !== null && <span className="rounded-full bg-white/10 px-3 py-1 font-semibold text-white">{value}</span>}
      <span className="px-1 text-navy-muted">
        Received{' '}
        <time dateTime={lead.created_at} title={new Date(lead.created_at).toLocaleString()}>
          {formatTimeSince(new Date(lead.created_at), currentTime)}
        </time>
      </span>
    </div>
  )
}
