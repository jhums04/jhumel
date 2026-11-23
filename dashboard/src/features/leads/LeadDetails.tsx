import type { ReactNode } from 'react'
import { formatCurrency } from '../../lib/format'
import type { LeadDetails as LeadDetailsData } from './leadsApi'
import { LeadReplyDraft } from './LeadReplyDraft'
import { LeadStageSelect } from './LeadStageSelect'
import { ScoreBadge } from './ScoreBadge'
import { getBudgetLabel, getServiceLabel, getTimelineLabel } from './serviceLabels'

function DetailSection({ title, children }: { title: string; children: ReactNode }) {
  return (
    <section className="border-t border-gray-200 px-6 py-6">
      <h3 className="mb-4 text-sm font-semibold text-gray-600">{title}</h3>
      {children}
    </section>
  )
}

function DetailRow({ label, children }: { label: string; children: ReactNode }) {
  return (
    <div className="grid grid-cols-[7rem_1fr] gap-3 py-1">
      <dt className="text-gray-600">{label}</dt>
      <dd className="min-w-0 break-words">{children}</dd>
    </div>
  )
}

export function LeadDetails({ lead }: { lead: LeadDetailsData }) {
  const submittedAt = new Date(lead.created_at)

  return (
    <>
      <DetailSection title="Status">
        <div className="flex flex-wrap items-end justify-between gap-3">
          <LeadStageSelect lead={lead} />
          <ScoreBadge score={lead.score} />
        </div>
        {lead.score_reason !== null && <p className="mt-3">{lead.score_reason}</p>}
      </DetailSection>

      <DetailSection title="Request">
        <dl>
          <DetailRow label="Email">
            <a href={`mailto:${lead.email}`} className="text-blue underline hover:text-blue-dark">
              {lead.email}
            </a>
          </DetailRow>
          {lead.phone !== null && (
            <DetailRow label="Phone">
              <a href={`tel:${lead.phone}`} className="text-blue underline hover:text-blue-dark">
                {lead.phone}
              </a>
            </DetailRow>
          )}
          <DetailRow label="Services">
            {lead.services.length === 0 ? 'None selected' : lead.services.map(getServiceLabel).join(', ')}
          </DetailRow>
          {lead.timeline !== null && <DetailRow label="Timeline">{getTimelineLabel(lead.timeline)}</DetailRow>}
          {lead.budget !== null && <DetailRow label="Budget">{getBudgetLabel(lead.budget)}</DetailRow>}
          {lead.estimated_price !== null && (
            <DetailRow label="Estimate">{formatCurrency(lead.estimated_price)}</DetailRow>
          )}
          <DetailRow label="Submitted">
            <time dateTime={lead.created_at}>{submittedAt.toLocaleString()}</time>
          </DetailRow>
        </dl>
        {lead.message !== null && <p className="mt-4 whitespace-pre-line">{lead.message}</p>}
      </DetailSection>

      <DetailSection title="Reply draft">
        {/* Keyed on whether a draft exists, so the fields fill in when the score and draft arrive live. */}
        <LeadReplyDraft key={`${lead.id}-${lead.draft_body === null ? 'waiting' : 'ready'}`} lead={lead} />
      </DetailSection>
    </>
  )
}
