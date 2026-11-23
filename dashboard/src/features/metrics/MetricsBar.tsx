import { formatCompactCurrency, formatDuration, formatPercent } from '../../lib/format'
import type { PipelineMetrics } from './calculatePipelineMetrics'

type StatTileProps = {
  label: string
  // Null shows emptyText instead, in body size: a sentence should not pose as a figure.
  value: string | null
  emptyText?: string
  detail: string
}

function StatTile({ label, value, emptyText, detail }: StatTileProps) {
  return (
    <div className="rounded-xl border border-gray-200 bg-white p-4">
      <dt className="text-sm text-gray-600">{label}</dt>
      {value === null ? (
        <dd className="mt-1 py-1 font-semibold text-gray-600">{emptyText}</dd>
      ) : (
        <dd className="mt-1 text-2xl font-semibold">{value}</dd>
      )}
      <dd className="mt-1 text-sm text-gray-600">{detail}</dd>
    </div>
  )
}

function plural(count: number, noun: string): string {
  return `${count} ${noun}${count === 1 ? '' : 's'}`
}

export function MetricsBar({ metrics }: { metrics: PipelineMetrics }) {
  const { conversionRate, averageResponseMilliseconds } = metrics

  return (
    <dl aria-label="Pipeline summary" className="grid grid-cols-2 gap-3 lg:grid-cols-4">
      <StatTile label="Leads today" value={String(metrics.leadsToday)} detail={`${metrics.totalLeads} in total`} />
      <StatTile
        label="Open pipeline"
        value={formatCompactCurrency(metrics.openPipelineValue)}
        detail={`across ${plural(metrics.openLeads, 'open lead')}`}
      />
      <StatTile
        label="Conversion rate"
        value={conversionRate === null ? null : formatPercent(conversionRate)}
        emptyText="No closed leads yet"
        detail={`${metrics.wonLeads} won · ${metrics.lostLeads} lost`}
      />
      <StatTile
        label="Avg. response time"
        value={averageResponseMilliseconds === null ? null : formatDuration(averageResponseMilliseconds)}
        emptyText="No replies yet"
        detail={`across ${plural(metrics.repliedLeads, 'replied lead')}`}
      />
    </dl>
  )
}
